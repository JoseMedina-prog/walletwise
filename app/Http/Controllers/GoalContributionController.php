<?php

namespace App\Http\Controllers;

use App\Http\Requests\GoalContributionRequest;
use App\Models\Goal;
use App\Models\GoalContribution;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GoalContributionController extends Controller
{
    public function store(GoalContributionRequest $request, Goal $goal): RedirectResponse
    {
        $this->authorize('update', $goal);

        DB::transaction(function () use ($request, $goal) {
            GoalContribution::create([
                'goal_id'           => $goal->id,
                'transaction_id'    => null,
                'amount'            => $request->validated('amount'),
                'contribution_date' => $request->validated('contribution_date'),
                'note'              => $request->validated('note'),
            ]);

            $goal->increment('current_amount', $request->validated('amount'));
            $goal->refresh()->checkCompletion();
        });

        return redirect()
            ->route('goals.edit', $goal)
            ->with('status', 'Aporte registrado.');
    }

    public function destroy(Goal $goal, GoalContribution $contribution): RedirectResponse
    {
        $this->authorize('update', $goal);
        abort_unless($contribution->goal_id === $goal->id, 404);

        DB::transaction(function () use ($goal, $contribution) {
            $amount = (float) $contribution->amount;
            $contribution->delete();

            $newCurrent = max(0, (float) $goal->current_amount - $amount);
            $goal->update(['current_amount' => $newCurrent]);

            if ($goal->is_completed && $newCurrent < (float) $goal->target_amount) {
                $goal->update([
                    'is_completed' => false,
                    'completed_at' => null,
                ]);
            }
        });

        return redirect()
            ->route('goals.edit', $goal)
            ->with('status', 'Aporte eliminado.');
    }
}