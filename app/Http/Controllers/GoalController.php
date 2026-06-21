<?php

namespace App\Http\Controllers;

use App\Http\Requests\GoalRequest;
use App\Models\Goal;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class GoalController extends Controller
{
    public function index(Request $request): View
    {
        $userId = Auth::id();
        $filter = $request->query('filter', 'active'); // active | completed | all

        $query = Goal::query()
            ->where('user_id', $userId)
            ->orderByDesc('is_completed')
            ->orderBy('target_date');

        if ($filter === 'active') {
            $query->where('is_completed', false);
        } elseif ($filter === 'completed') {
            $query->where('is_completed', true);
        }

        $goals = $query->get()->map(function (Goal $g) {
            $g->percent = $g->percentReached();
            $g->on_track = $g->isOnTrack();
            $g->monthly_suggestion = $g->suggestedMonthlyContribution();
            $g->projected = $g->projectedCompletionDate();
            return $g;
        });

        $summary = [
            'total_target'  => (float) $goals->sum('target_amount'),
            'total_current' => (float) $goals->sum('current_amount'),
            'completed'     => $goals->where('is_completed', true)->count(),
            'active'        => $goals->where('is_completed', false)->count(),
        ];

        return view('goals.index', compact('goals', 'summary', 'filter'));
    }

    public function create(): View
    {
        return view('goals.create');
    }

    public function store(GoalRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $data['current_amount'] = $data['current_amount'] ?? 0;

        Goal::create($data);

        return redirect()
            ->route('goals.index')
            ->with('status', 'Meta creada.');
    }

    public function edit(Goal $goal): View
    {
        $this->authorize('update', $goal);
        return view('goals.edit', compact('goal'));
    }

    public function update(GoalRequest $request, Goal $goal): RedirectResponse
    {
        $this->authorize('update', $goal);

        $goal->update($request->validated());
        $goal->checkCompletion();

        return redirect()
            ->route('goals.index')
            ->with('status', 'Meta actualizada.');
    }

    public function destroy(Goal $goal): RedirectResponse
    {
        $this->authorize('delete', $goal);

        $goal->delete();

        return redirect()
            ->route('goals.index')
            ->with('status', 'Meta eliminada.');
    }
}