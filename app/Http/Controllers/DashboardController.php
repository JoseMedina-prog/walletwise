<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
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

        $savingsRate = $monthIncome > 0
            ? round((($monthIncome - $monthExpense) / $monthIncome) * 100, 1)
            : 0.0;

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

        return view('dashboard', compact(
            'totalBalance', 'totalIncome', 'totalExpense',
            'monthIncome', 'monthExpense', 'savingsRate',
            'monthlyData', 'expensesByCategory', 'recentTransactions'
        ));
    }
}
