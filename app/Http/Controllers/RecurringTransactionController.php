<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecurringTransactionRequest;
use App\Models\Category;
use App\Models\RecurringTransaction;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RecurringTransactionController extends Controller
{
    public function index(Request $request): View
    {
        $userId = Auth::id();
        $filter = $request->query('filter', 'active'); // active | all | due

        $query = RecurringTransaction::query()
            ->where('user_id', $userId)
            ->with('category')
            ->orderBy('next_occurrence');

        if ($filter === 'active') {
            $query->where('is_active', true);
        } elseif ($filter === 'due') {
            $query->due();
        }

        $recurrings = $query->get()->map(function (RecurringTransaction $r) {
            $r->is_due = $r->is_active && $r->isDue();
            $r->label = $r->frequencyLabel();
            return $r;
        });

        $summary = [
            'total'   => $recurrings->where('is_active', true)->count(),
            'due'     => $recurrings->where('is_due', true)->count(),
            'income'  => (float) $recurrings->where('is_active', true)->where('type', 'income')->sum('amount'),
            'expense' => (float) $recurrings->where('is_active', true)->where('type', 'expense')->sum('amount'),
        ];

        return view('recurring.index', compact('recurrings', 'summary', 'filter'));
    }

    public function create(): View
    {
        $categories = Category::query()
            ->where('user_id', Auth::id())
            ->orderBy('type')->orderBy('name')
            ->get();

        return view('recurring.create', compact('categories'));
    }

    public function store(RecurringTransactionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['next_occurrence'] = $data['start_date'];

        RecurringTransaction::create($data);

        return redirect()
            ->route('recurring.index')
            ->with('status', 'Recurrente creado.');
    }

    public function edit(RecurringTransaction $recurring): View
    {
        $this->authorize('update', $recurring);

        $categories = Category::query()
            ->where('user_id', Auth::id())
            ->orderBy('type')->orderBy('name')
            ->get();

        return view('recurring.edit', [
            'recurring' => $recurring,
            'categories' => $categories,
        ]);
    }

    public function update(RecurringTransactionRequest $request, RecurringTransaction $recurring): RedirectResponse
    {
        $this->authorize('update', $recurring);

        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', false);

        $recurring->update($data);

        return redirect()
            ->route('recurring.index')
            ->with('status', 'Recurrente actualizado.');
    }

    public function destroy(RecurringTransaction $recurring): RedirectResponse
    {
        $this->authorize('delete', $recurring);

        $recurring->delete();

        return redirect()
            ->route('recurring.index')
            ->with('status', 'Recurrente eliminado.');
    }

    public function post(RecurringTransaction $recurring): RedirectResponse
    {
        $this->authorize('post', $recurring);

        $tx = $recurring->postNow();

        if (!$tx) {
            return back()->with('error', 'No se pudo registrar la transacción (recurrente inactivo o expirado).');
        }

        return redirect()
            ->route('transactions.edit', $tx)
            ->with('status', 'Transacción registrada. Ajusta la fecha si lo necesitas.');
    }
}