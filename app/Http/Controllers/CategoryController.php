<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->where('user_id', Auth::id())
            ->orderBy('type')
            ->orderBy('name')
            ->withCount('transactions')
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('categories.create');
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        Category::create([
            'user_id' => Auth::id(),
            'name' => $request->validated('name'),
            'type' => $request->validated('type'),
        ]);

        return redirect()
            ->route('categories.index')
            ->with('status', 'Categoría creada correctamente.');
    }

    public function edit(Category $category): View
    {
        $this->authorize('update', $category);

        return view('categories.edit', compact('category'));
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $this->authorize('update', $category);

        $category->update($request->validated());

        return redirect()
            ->route('categories.index')
            ->with('status', 'Categoría actualizada.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->authorize('delete', $category);

        if ($category->transactions()->exists()) {
            return redirect()
                ->back()
                ->with('error', 'No puedes eliminar una categoría con transacciones asociadas.');
        }

        Category::destroy($category->getKey());

        return redirect()
            ->route('categories.index')
            ->with('status', 'Categoría eliminada.');
    }
}
