<?php

namespace App\Http\Controllers;

use App\Http\Requests\BudgetRequest;
use App\Models\Budget;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BudgetController extends Controller
{
    public function index(Request $request): View
    {
        $userId = Auth::id();

        $budgets = Budget::query()
            ->where('user_id', $userId)
            ->with('category')
            ->orderByDesc('is_active')
            ->orderBy(
                Category::select('name')
                    ->whereColumn('categories.id', 'budgets.category_id')
            )
            ->get()
            ->map(function (Budget $b) {
                $b->spent = $b->spentInPeriod();
                $b->percent = $b->percentUsed();
                $b->remaining = $b->remaining();
                $b->level = $b->alertLevel();
                return $b;
            });

        $categories = Category::query()
            ->where('user_id', $userId)
            ->where('type', 'expense')
            ->orderBy('name')
            ->get();

        $summary = [
            'total'      => (float) $budgets->sum('monthly_amount'),
            'spent'      => (float) $budgets->sum('spent'),
            'over_count' => $budgets->where('level', 'over')->count(),
            'warn_count' => $budgets->where('level', 'warn')->count(),
        ];

        return view('budgets.index', compact('budgets', 'categories', 'summary'));
    }

    public function create(): View
    {
        $categories = Category::query()
            ->where('user_id', Auth::id())
            ->where('type', 'expense')
            ->whereDoesntHave('budget')
            ->orderBy('name')
            ->get();

        $preselectedCategoryId = (int) request()->query('category_id');

        return view('budgets.create', [
            'categories'             => $categories,
            'preselectedCategoryId'  => $preselectedCategoryId,
        ]);
    }

    public function store(BudgetRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id']  = Auth::id();
        $data['is_active'] = $request->boolean('is_active', true);

        $exists = Budget::where('user_id', Auth::id())
            ->where('category_id', $data['category_id'])
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['category_id' => 'Ya tienes un presupuesto para esta categoría. Edítalo en su lugar.']);
        }

        Budget::create($data);

        return redirect()
            ->route('budgets.index')
            ->with('status', 'Presupuesto creado.');
    }

    public function edit(Budget $budget): View
    {
        $this->authorize('update', $budget);

        $categories = Category::query()
            ->where('user_id', Auth::id())
            ->where('type', 'expense')
            ->orderBy('name')
            ->get();

        return view('budgets.edit', [
            'budget'     => $budget,
            'categories' => $categories,
        ]);
    }

    public function update(BudgetRequest $request, Budget $budget): RedirectResponse
    {
        $this->authorize('update', $budget);

        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', false);

        $budget->update($data);

        return redirect()
            ->route('budgets.index')
            ->with('status', 'Presupuesto actualizado.');
    }

    public function destroy(Budget $budget): RedirectResponse
    {
        $this->authorize('delete', $budget);

        $budget->delete();

        return redirect()
            ->route('budgets.index')
            ->with('status', 'Presupuesto eliminado.');
    }
}