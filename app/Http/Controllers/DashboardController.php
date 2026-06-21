<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Goal;
use App\Models\RecurringTransaction;
use App\Models\Transaction;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $userId = Auth::id();
        $now = Carbon::now();

        $totalIncome = (float) Transaction::where('user_id', $userId)->where('type', 'income')->sum('amount');
        $totalExpense = (float) Transaction::where('user_id', $userId)->where('type', 'expense')->sum('amount');
        $totalBalance = $totalIncome - $totalExpense;

        $monthIncome = (float) Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->whereYear('transaction_date', $now->year)
            ->whereMonth('transaction_date', $now->month)
            ->sum('amount');

        $monthExpense = (float) Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereYear('transaction_date', $now->year)
            ->whereMonth('transaction_date', $now->month)
            ->sum('amount');

        $prevMonth = $now->copy()->subMonthNoOverflow();

        $prevMonthIncome = (float) Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->whereYear('transaction_date', $prevMonth->year)
            ->whereMonth('transaction_date', $prevMonth->month)
            ->sum('amount');

        $prevMonthExpense = (float) Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereYear('transaction_date', $prevMonth->year)
            ->whereMonth('transaction_date', $prevMonth->month)
            ->sum('amount');

        $savingsRate = $monthIncome > 0
            ? round((($monthIncome - $monthExpense) / $monthIncome) * 100, 1)
            : 0.0;

        $incomeTrend    = self::delta($monthIncome, $prevMonthIncome, inverse: false);
        $expenseTrend   = self::delta($monthExpense, $prevMonthExpense, inverse: true);
        $savingsTrend   = self::delta(
            $monthIncome > 0 ? ($monthIncome - $monthExpense) / max($monthIncome, 1) * 100 : 0,
            $prevMonthIncome > 0 ? ($prevMonthIncome - $prevMonthExpense) / max($prevMonthIncome, 1) * 100 : 0,
            inverse: false
        );

        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = $now->copy()->subMonths($i);
            $income = (float) Transaction::where('user_id', $userId)
                ->where('type', 'income')
                ->whereYear('transaction_date', $m->year)
                ->whereMonth('transaction_date', $m->month)
                ->sum('amount');
            $expense = (float) Transaction::where('user_id', $userId)
                ->where('type', 'expense')
                ->whereYear('transaction_date', $m->year)
                ->whereMonth('transaction_date', $m->month)
                ->sum('amount');
            $monthlyData[] = [
                'label' => $m->translatedFormat('M Y'),
                'income' => $income,
                'expense' => $expense,
                'balance' => $income - $expense,
            ];
        }

        $expensesByCategory = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereYear('transaction_date', $now->year)
            ->whereMonth('transaction_date', $now->month)
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->with('category')
            ->get()
            ->map(fn ($row) => [
                'name' => $row->category->name,
                'total' => (float) $row->total,
            ])
            ->sortByDesc('total')
            ->values();

        $recentTransactions = Transaction::where('user_id', $userId)
            ->with('category')
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $budgets = Budget::query()
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->with('category')
            ->get()
            ->sortByDesc(fn (Budget $b) => $b->percentUsed())
            ->take(5)
            ->map(function (Budget $b) {
                $b->spent = $b->spentInPeriod();
                $b->percent = $b->percentUsed();
                $b->level = $b->alertLevel();
                return $b;
            })
            ->values();

        $dueRecurrings = RecurringTransaction::query()
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->where('next_occurrence', '<=', CarbonImmutable::now()->toDateString())
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', CarbonImmutable::now()->toDateString());
            })
            ->with('category')
            ->orderBy('next_occurrence')
            ->limit(5)
            ->get()
            ->map(fn (RecurringTransaction $r) => tap($r, fn ($r) => $r->label = $r->frequencyLabel()));

        $activeGoals = Goal::query()
            ->where('user_id', $userId)
            ->where('is_completed', false)
            ->orderByDesc('created_at')
            ->limit(3)
            ->get()
            ->each(function (Goal $g) {
                $g->percent = $g->percentReached();
                $g->monthly_suggestion = $g->suggestedMonthlyContribution();
            });

        return view('dashboard', compact(
            'totalBalance', 'totalIncome', 'totalExpense',
            'monthIncome', 'monthExpense', 'savingsRate',
            'prevMonthIncome', 'prevMonthExpense',
            'incomeTrend', 'expenseTrend', 'savingsTrend',
            'monthlyData', 'expensesByCategory', 'recentTransactions',
            'budgets', 'dueRecurrings', 'activeGoals'
        ));
    }

    /**
     * Calcula delta porcentual entre valor actual y previo.
     *
     * @return array{label: string|null, tone: string}|null
     */
    private static function delta(float $current, float $previous, bool $inverse): ?array
    {
        if ($previous == 0.0 && $current == 0.0) {
            return ['label' => '0%', 'tone' => 'neutral'];
        }
        if ($previous == 0.0) {
            return ['label' => 'Nuevo', 'tone' => $inverse ? 'expense' : 'income'];
        }

        $pct = round((($current - $previous) / abs($previous)) * 100, 1);
        if ($pct == 0.0) {
            return ['label' => '0%', 'tone' => 'neutral'];
        }

        $isUp = $pct > 0;
        $good = $inverse ? !$isUp : $isUp;

        return [
            'label' => ($isUp ? '+' : '') . number_format($pct, 1) . '%',
            'tone'  => $good ? 'income' : 'expense',
        ];
    }
}
