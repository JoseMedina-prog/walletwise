<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $userId = Auth::id();

        $query = Transaction::query()
            ->where('user_id', $userId)
            ->with('category')
            ->orderByDesc('transaction_date')
            ->orderByDesc('id');

        $from = $request->query('from');
        $to = $request->query('to');
        $type = $request->query('type');
        $categoryId = $request->query('category_id');

        if ($from) {
            $query->whereDate('transaction_date', '>=', $from);
        }
        if ($to) {
            $query->whereDate('transaction_date', '<=', $to);
        }
        if (in_array($type, ['income', 'expense'], true)) {
            $query->where('type', $type);
        }
        if ($categoryId && is_numeric($categoryId)) {
            $query->where('category_id', (int) $categoryId);
        }

        $transactions = $query->paginate(15)->withQueryString();
        $categories = $this->userCategories($userId);

        $summary = [
            'income' => (float) (clone $query)->where('type', 'income')->sum('amount'),
            'expense' => (float) (clone $query)->where('type', 'expense')->sum('amount'),
        ];
        $summary['balance'] = $summary['income'] - $summary['expense'];

        return view('transactions.index', compact(
            'transactions', 'categories', 'from', 'to', 'type', 'categoryId', 'summary'
        ));
    }

    public function create(): View
    {
        return view('transactions.create', [
            'categories' => $this->userCategories(Auth::id()),
        ]);
    }

    public function store(TransactionRequest $request): RedirectResponse
    {
        Transaction::create([
            'user_id' => Auth::id(),
            'category_id' => $request->validated('category_id'),
            'type' => $request->validated('type'),
            'amount' => $request->validated('amount'),
            'description' => $request->validated('description'),
            'transaction_date' => $request->validated('transaction_date'),
        ]);

        return redirect()
            ->route('transactions.index')
            ->with('status', 'Transacción registrada.');
    }

    public function edit(Transaction $transaction): View
    {
        $this->authorize('update', $transaction);

        return view('transactions.edit', [
            'transaction' => $transaction,
            'categories' => $this->userCategories(Auth::id()),
        ]);
    }

    public function update(TransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        $this->authorize('update', $transaction);

        $transaction->update($request->validated());

        return redirect()
            ->route('transactions.index')
            ->with('status', 'Transacción actualizada.');
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        $this->authorize('delete', $transaction);

        Transaction::destroy($transaction->getKey());

        return redirect()
            ->route('transactions.index')
            ->with('status', 'Transacción eliminada.');
    }

    public function duplicate(Transaction $transaction): RedirectResponse
    {
        $this->authorize('duplicate', $transaction);

        $copy = Transaction::create([
            'user_id' => Auth::id(),
            'category_id' => $transaction->category_id,
            'type' => $transaction->type,
            'amount' => $transaction->amount,
            'description' => $transaction->description,
            'transaction_date' => now()->toDateString(),
        ]);

        return redirect()
            ->route('transactions.edit', $copy)
            ->with('status', 'Transacción duplicada. Ajusta la fecha si lo necesitas.');
    }

    public function suggestCategory(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'min:3', 'max:100'],
            'type' => ['nullable', 'in:income,expense'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:5'],
        ]);

        $term = '%'.$validated['q'].'%';
        $type = $validated['type'] ?? null;
        $limit = $validated['limit'] ?? 3;
        $userId = Auth::id();

        $suggestions = Category::query()
            ->whereHas('transactions', function ($q) use ($term, $userId, $type) {
                $q->where('user_id', $userId)
                    ->where('description', 'LIKE', $term);

                if ($type) {
                    $q->where('type', $type);
                }
            })
            ->withCount(['transactions as match_count' => function ($q) use ($term, $userId, $type) {
                $q->where('user_id', $userId)
                    ->where('description', 'LIKE', $term);

                if ($type) {
                    $q->where('type', $type);
                }
            }])
            ->orderByDesc('match_count')
            ->limit($limit)
            ->get(['id', 'name', 'type']);

        return response()->json([
            'suggestions' => $suggestions->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'type' => $c->type,
                'match_count' => (int) $c->match_count,
            ]),
        ]);
    }

    private function userCategories(int $userId)
    {
        return Category::query()
            ->where('user_id', $userId)
            ->orderBy('type')
            ->orderBy('name')
            ->get();
    }
}
