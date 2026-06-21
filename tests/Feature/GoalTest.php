<?php

namespace Tests\Feature;

use App\Models\Goal;
use App\Models\GoalContribution;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoalTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(): User
    {
        return User::factory()->create();
    }

    private function makeGoal(?User $user = null, array $overrides = []): Goal
    {
        $user ??= $this->makeUser();
        $start = CarbonImmutable::now()->startOfMonth();
        return Goal::factory()->create(array_merge([
            'user_id'        => $user->id,
            'name'           => 'Vacaciones',
            'target_amount'  => 1000,
            'current_amount' => 0,
            'start_date'     => $start->toDateString(),
            'target_date'    => $start->addMonths(6)->toDateString(),
        ], $overrides));
    }

    // ─────────────────────────────────────────────────────────────
    //  AUTHORIZATION
    // ─────────────────────────────────────────────────────────────

    public function test_guest_cannot_view_goals(): void
    {
        $this->get('/goals')->assertRedirect('/login');
    }

    public function test_user_cannot_edit_others_goal(): void
    {
        $user  = $this->makeUser();
        $other = $this->makeUser();
        $goal = $this->makeGoal($other);

        $this->actingAs($user)
            ->get(route('goals.edit', $goal))
            ->assertForbidden();

        $this->actingAs($user)
            ->delete(route('goals.destroy', $goal))
            ->assertForbidden();
    }

    // ─────────────────────────────────────────────────────────────
    //  CRUD
    // ─────────────────────────────────────────────────────────────

    public function test_user_can_create_goal(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
            ->post('/goals', [
                'name'          => 'Coche nuevo',
                'description'   => 'Para ahorrar para el coche',
                'target_amount' => 5000,
                'target_date'   => now()->addMonths(12)->toDateString(),
            ])
            ->assertRedirect(route('goals.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('goals', [
            'user_id' => $user->id,
            'name' => 'Coche nuevo',
            'target_amount' => '5000.00',
            'current_amount' => '0.00',
        ]);
    }

    public function test_validates_required_fields(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
            ->post('/goals', [])
            ->assertSessionHasErrors(['name', 'target_amount']);
    }

    public function test_validates_target_amount_positive(): void
    {
        $user = $this->makeUser();
        $this->actingAs($user)
            ->post('/goals', ['name' => 'X', 'target_amount' => 0])
            ->assertSessionHasErrors('target_amount');
    }

    public function test_validates_target_date_not_past(): void
    {
        $user = $this->makeUser();
        $this->actingAs($user)
            ->post('/goals', [
                'name' => 'X',
                'target_amount' => 100,
                'target_date' => '2020-01-01',
            ])
            ->assertSessionHasErrors('target_date');
    }

    public function test_user_can_update_goal(): void
    {
        $user = $this->makeUser();
        $goal = $this->makeGoal($user, ['name' => 'Original']);

        $this->actingAs($user)
            ->put(route('goals.update', $goal), [
                'name' => 'Actualizado',
                'target_amount' => 2000,
            ])
            ->assertRedirect(route('goals.index'));

        $goal->refresh();
        $this->assertSame('Actualizado', $goal->name);
        $this->assertEquals('2000.00', (string) $goal->target_amount);
    }

    public function test_user_can_delete_goal(): void
    {
        $user = $this->makeUser();
        $goal = $this->makeGoal($user);

        $this->actingAs($user)
            ->delete(route('goals.destroy', $goal))
            ->assertRedirect(route('goals.index'));

        $this->assertDatabaseMissing('goals', ['id' => $goal->id]);
    }

    // ─────────────────────────────────────────────────────────────
    //  CALCULATION
    // ─────────────────────────────────────────────────────────────

    public function test_percent_reached(): void
    {
        $goal = $this->makeGoal($this->makeUser(), [
            'target_amount' => 1000,
            'current_amount' => 250,
        ]);
        $this->assertSame(25.0, $goal->percentReached());

        $goal->update(['current_amount' => 1000]);
        $this->assertSame(100.0, $goal->percentReached());
    }

    public function test_check_completion_marks_at_100(): void
    {
        $goal = $this->makeGoal($this->makeUser(), [
            'target_amount' => 100,
            'current_amount' => 100,
        ]);

        $this->assertTrue($goal->checkCompletion());
        $goal->refresh();
        $this->assertTrue($goal->is_completed);
        $this->assertNotNull($goal->completed_at);
    }

    public function test_suggested_monthly_contribution(): void
    {
        $user = $this->makeUser();
        $start = CarbonImmutable::now()->startOfMonth();
        $goal = $this->makeGoal($user, [
            'target_amount'  => 1200,
            'current_amount' => 0,
            'start_date'     => $start->toDateString(),
            'target_date'    => $start->addMonths(12)->toDateString(),
        ]);

        $suggestion = $goal->suggestedMonthlyContribution();
        $this->assertNotNull($suggestion);
        $this->assertEqualsWithDelta(100, $suggestion, 1);
    }

    public function test_cascade_deletes_contributions(): void
    {
        $user = $this->makeUser();
        $goal = $this->makeGoal($user);
        GoalContribution::factory()->create(['goal_id' => $goal->id, 'amount' => 50]);

        $this->assertDatabaseCount('goal_contributions', 1);

        $goal->delete();

        $this->assertDatabaseCount('goal_contributions', 0);
    }

    public function test_cascade_on_user_delete(): void
    {
        $user = $this->makeUser();
        $goal = $this->makeGoal($user);

        $user->delete();

        $this->assertDatabaseMissing('goals', ['id' => $goal->id]);
    }

    // ─────────────────────────────────────────────────────────────
    //  CONTRIBUTIONS
    // ─────────────────────────────────────────────────────────────

    public function test_user_can_add_contribution(): void
    {
        $user = $this->makeUser();
        $goal = $this->makeGoal($user, ['target_amount' => 1000, 'current_amount' => 0]);

        $this->actingAs($user)
            ->post(route('goals.contributions.store', $goal), [
                'amount' => 100,
                'contribution_date' => now()->toDateString(),
                'note' => 'Ahorro del mes',
            ])
            ->assertRedirect();

        $this->assertDatabaseCount('goal_contributions', 1);
        $goal->refresh();
        $this->assertEquals(100.0, (float) $goal->current_amount);
    }

    public function test_contribution_marks_goal_completed(): void
    {
        $user = $this->makeUser();
        $goal = $this->makeGoal($user, ['target_amount' => 50, 'current_amount' => 0]);

        $this->actingAs($user)
            ->post(route('goals.contributions.store', $goal), [
                'amount' => 50,
                'contribution_date' => now()->toDateString(),
            ])
            ->assertRedirect();

        $goal->refresh();
        $this->assertTrue($goal->is_completed);
    }

    public function test_user_can_delete_contribution(): void
    {
        $user = $this->makeUser();
        $goal = $this->makeGoal($user, ['target_amount' => 1000, 'current_amount' => 200]);
        $c = GoalContribution::factory()->create([
            'goal_id' => $goal->id,
            'amount'  => 100,
        ]);

        $this->actingAs($user)
            ->delete(route('goals.contributions.destroy', [$goal, $c]))
            ->assertRedirect();

        $this->assertDatabaseCount('goal_contributions', 0);
        $goal->refresh();
        $this->assertEquals(100.0, (float) $goal->current_amount);
    }

    public function test_cannot_delete_contribution_from_other_goal(): void
    {
        $user = $this->makeUser();
        $goal1 = $this->makeGoal($user);
        $goal2 = $this->makeGoal($user);
        $c = GoalContribution::factory()->create(['goal_id' => $goal1->id]);

        $this->actingAs($user)
            ->delete(route('goals.contributions.destroy', [$goal2, $c]))
            ->assertNotFound();
    }

    public function test_user_cannot_add_contribution_to_others_goal(): void
    {
        $user  = $this->makeUser();
        $other = $this->makeUser();
        $goal = $this->makeGoal($other);

        $this->actingAs($user)
            ->post(route('goals.contributions.store', $goal), [
                'amount' => 50,
                'contribution_date' => now()->toDateString(),
            ])
            ->assertForbidden();
    }

    // ─────────────────────────────────────────────────────────────
    //  VIEWS
    // ─────────────────────────────────────────────────────────────

    public function test_index_shows_goals(): void
    {
        $user = $this->makeUser();
        $goal = $this->makeGoal($user, ['name' => 'Vacaciones']);

        $this->actingAs($user)
            ->get('/goals')
            ->assertOk()
            ->assertSee('Vacaciones');
    }

    public function test_index_filter_completed(): void
    {
        $user = $this->makeUser();
        $this->makeGoal($user, ['name' => 'ACTIVA']);
        $this->makeGoal($user, ['name' => 'COMPLETADA', 'is_completed' => true]);

        $response = $this->actingAs($user)
            ->get('/goals?filter=completed')
            ->assertOk();
        $response->assertSee('COMPLETADA');
        $response->assertDontSee('ACTIVA');
    }
}