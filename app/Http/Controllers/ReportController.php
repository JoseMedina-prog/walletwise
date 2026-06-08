<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $userId = Auth::id();
        $from = $request->query('from') ?: now()->startOfMonth()->toDateString();
        $to = $request->query('to') ?: now()->toDateString();
        $type = $request->query('type');
        $categoryId = $request->query('category_id');

        $base = Transaction::query()
            ->where('user_id', $userId)
            ->whereDate('transaction_date', '>=', $from)
            ->whereDate('transaction_date', '<=', $to);

        if (in_array($type, ['income', 'expense'], true)) {
            $base->where('type', $type);
        }
        if ($categoryId && is_numeric($categoryId)) {
            $base->where('category_id', (int) $categoryId);
        }

        $income = (float) (clone $base)->where('type', 'income')->sum('amount');
        $expense = (float) (clone $base)->where('type', 'expense')->sum('amount');
        $balance = $income - $expense;
        $count = (clone $base)->count();

        $breakdownExpense = (clone $base)
            ->where('type', 'expense')
            ->select('category_id', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as tx_count'))
            ->groupBy('category_id')
            ->with('category')
            ->get()
            ->map(fn ($r) => [
                'name' => $r->category->name,
                'total' => (float) $r->total,
                'tx_count' => (int) $r->tx_count,
            ])
            ->sortByDesc('total')
            ->values();

        $breakdownIncome = (clone $base)
            ->where('type', 'income')
            ->select('category_id', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as tx_count'))
            ->groupBy('category_id')
            ->with('category')
            ->get()
            ->map(fn ($r) => [
                'name' => $r->category->name,
                'total' => (float) $r->total,
                'tx_count' => (int) $r->tx_count,
            ])
            ->sortByDesc('total')
            ->values();

        $transactions = (clone $base)
            ->with('category')
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        $categories = Category::query()
            ->where('user_id', $userId)
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view('reports.index', compact(
            'from', 'to', 'type', 'categoryId',
            'income', 'expense', 'balance', 'count',
            'breakdownExpense', 'breakdownIncome',
            'transactions', 'categories'
        ));
    }
}
