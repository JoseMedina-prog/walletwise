<?php

namespace Tests\Feature;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(): User
    {
        return User::factory()->create();
    }

    private function makeExpenseCategory(User $user, string $name = 'Comida'): Category
    {
        return Category::factory()->for($user)->create(['type' => 'expense', 'name' => $name]);
    }

    // ─────────────────────────────────────────────────────────────
    //  AUTHORIZATION
    // ─────────────────────────────────────────────────────────────

    public function test_guest_cannot_view_budgets(): void
    {
        $this->get('/budgets')->assertRedirect('/login');
    }

    public function test_user_cannot_view_other_users_budget(): void
    {
        $user  = $this->makeUser();
        $other = $this->makeUser();
        $cat   = $this->makeExpenseCategory($other);
        $budget = Budget::factory()->create(['user_id' => $other->id, 'category_id' => $cat->id]);

        $this->actingAs($user)
            ->get(route('budgets.edit', $budget))
            ->assertForbidden();

        $this->actingAs($user)
            ->delete(route('budgets.destroy', $budget))
            ->assertForbidden();
    }

    // ─────────────────────────────────────────────────────────────
    //  INDEX
    // ─────────────────────────────────────────────────────────────

    public function test_user_can_view_their_budgets_index(): void
    {
        $user = $this->makeUser();
        $cat  = $this->makeExpenseCategory($user, 'Comida');
        Budget::factory()->create([
            'user_id'     => $user->id,
            'category_id' => $cat->id,
            'monthly_amount' => 500,
        ]);

        $this->actingAs($user)
            ->get('/budgets')
            ->assertOk()
            ->assertSee('Comida')
            ->assertSee('500');
    }

    public function test_index_does_not_show_other_users_budgets(): void
    {
        $user  = $this->makeUser();
        $other = $this->makeUser();

        $cat1 = $this->makeExpenseCategory($user,  'Mía');
        $cat2 = $this->makeExpenseCategory($other, 'Otra');

        Budget::factory()->create(['user_id' => $user->id,  'category_id' => $cat1->id]);
        Budget::factory()->create(['user_id' => $other->id, 'category_id' => $cat2->id]);

        $this->actingAs($user)
            ->get('/budgets')
            ->assertSee('Mía')
            ->assertDontSee('Otra');
    }

    // ─────────────────────────────────────────────────────────────
    //  STORE
    // ─────────────────────────────────────────────────────────────

    public function test_user_can_create_budget(): void
    {
        $user = $this->makeUser();
        $cat  = $this->makeExpenseCategory($user);

        $this->actingAs($user)
            ->post('/budgets', [
                'category_id'     => $cat->id,
                'monthly_amount'  => 300.50,
                'alert_threshold' => 75,
                'is_active'       => 1,
            ])
            ->assertRedirect(route('budgets.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('budgets', [
            'user_id'         => $user->id,
            'category_id'     => $cat->id,
            'monthly_amount'  => '300.50',
            'alert_threshold' => 75,
            'is_active'       => true,
        ]);
    }

    public function test_cannot_create_two_budgets_for_same_category(): void
    {
        $user = $this->makeUser();
        $cat  = $this->makeExpenseCategory($user);
        Budget::factory()->create(['user_id' => $user->id, 'category_id' => $cat->id]);

        $this->actingAs($user)
            ->post('/budgets', [
                'category_id'     => $cat->id,
                'monthly_amount'  => 200,
                'alert_threshold' => 80,
                'is_active'       => 1,
            ])
            ->assertSessionHasErrors('category_id');
    }

    public function test_cannot_create_budget_for_income_category(): void
    {
        $user = $this->makeUser();
        $incomeCat = Category::factory()->for($user)->create(['type' => 'income']);

        $this->actingAs($user)
            ->post('/budgets', [
                'category_id'     => $incomeCat->id,
                'monthly_amount'  => 200,
                'alert_threshold' => 80,
                'is_active'       => 1,
            ])
            ->assertSessionHasErrors('category_id');
    }

    public function test_cannot_use_other_users_category(): void
    {
        $user  = $this->makeUser();
        $other = $this->makeUser();
        $otherCat = $this->makeExpenseCategory($other);

        $this->actingAs($user)
            ->post('/budgets', [
                'category_id'     => $otherCat->id,
                'monthly_amount'  => 200,
                'alert_threshold' => 80,
                'is_active'       => 1,
            ])
            ->assertSessionHasErrors('category_id');
    }

    public function test_validates_required_fields(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
            ->post('/budgets', [])
            ->assertSessionHasErrors(['category_id', 'monthly_amount', 'alert_threshold']);
    }

    public function test_validates_amount_positive(): void
    {
        $user = $this->makeUser();
        $cat  = $this->makeExpenseCategory($user);

        $this->actingAs($user)
            ->post('/budgets', [
                'category_id'     => $cat->id,
                'monthly_amount'  => 0,
                'alert_threshold' => 80,
                'is_active'       => 1,
            ])
            ->assertSessionHasErrors('monthly_amount');
    }

    public function test_validates_threshold_range(): void
    {
        $user = $this->makeUser();
        $cat  = $this->makeExpenseCategory($user);

        $this->actingAs($user)
            ->post('/budgets', [
                'category_id'     => $cat->id,
                'monthly_amount'  => 100,
                'alert_threshold' => 150,
                'is_active'       => 1,
            ])
            ->assertSessionHasErrors('alert_threshold');
    }

    // ─────────────────────────────────────────────────────────────
    //  UPDATE
    // ─────────────────────────────────────────────────────────────

    public function test_user_can_update_their_budget(): void
    {
        $user = $this->makeUser();
        $cat  = $this->makeExpenseCategory($user);
        $budget = Budget::factory()->create([
            'user_id'         => $user->id,
            'category_id'     => $cat->id,
            'monthly_amount'  => 100,
            'alert_threshold' => 80,
        ]);

        $this->actingAs($user)
            ->put(route('budgets.update', $budget), [
                'category_id'     => $cat->id,
                'monthly_amount'  => 500,
                'alert_threshold' => 90,
                'is_active'       => 0,
            ])
            ->assertRedirect(route('budgets.index'));

        $budget->refresh();
        $this->assertEquals('500.00', (string) $budget->monthly_amount);
        $this->assertEquals(90, $budget->alert_threshold);
        $this->assertFalse($budget->is_active);
    }

    // ─────────────────────────────────────────────────────────────
    //  DESTROY
    // ─────────────────────────────────────────────────────────────

    public function test_user_can_delete_their_budget(): void
    {
        $user = $this->makeUser();
        $cat  = $this->makeExpenseCategory($user);
        $budget = Budget::factory()->create(['user_id' => $user->id, 'category_id' => $cat->id]);

        $this->actingAs($user)
            ->delete(route('budgets.destroy', $budget))
            ->assertRedirect(route('budgets.index'));

        $this->assertDatabaseMissing('budgets', ['id' => $budget->id]);
    }

    // ─────────────────────────────────────────────────────────────
    //  CALCULATION LOGIC
    // ─────────────────────────────────────────────────────────────

    public function test_spent_in_period_sums_only_current_month_expenses(): void
    {
        $user = $this->makeUser();
        $cat  = $this->makeExpenseCategory($user);

        $budget = Budget::factory()->create([
            'user_id'        => $user->id,
            'category_id'    => $cat->id,
            'monthly_amount' => 500,
        ]);

        Transaction::factory()->for($user)->create([
            'category_id'      => $cat->id,
            'type'             => 'expense',
            'amount'           => 100,
            'transaction_date' => now()->toDateString(),
        ]);
        Transaction::factory()->for($user)->create([
            'category_id'      => $cat->id,
            'type'             => 'expense',
            'amount'           => 50,
            'transaction_date' => now()->subMonth()->toDateString(),
        ]);
        Transaction::factory()->for($user)->create([
            'category_id'      => $cat->id,
            'type'             => 'income',
            'amount'           => 999,
            'transaction_date' => now()->toDateString(),
        ]);

        $this->assertEquals(100.0, $budget->spentInPeriod());
        $this->assertEquals(20.0, $budget->percentUsed());
        $this->assertEquals(400.0, $budget->remaining());
    }

    public function test_alert_level_ok_below_threshold(): void
    {
        $user = $this->makeUser();
        $cat  = $this->makeExpenseCategory($user);
        $budget = Budget::factory()->create([
            'user_id'         => $user->id,
            'category_id'     => $cat->id,
            'monthly_amount'  => 1000,
            'alert_threshold' => 80,
        ]);

        Transaction::factory()->for($user)->create([
            'category_id'      => $cat->id,
            'type'             => 'expense',
            'amount'           => 500,
            'transaction_date' => now()->toDateString(),
        ]);

        $this->assertEquals('ok', $budget->alertLevel());
    }

    public function test_alert_level_warn_at_threshold(): void
    {
        $user = $this->makeUser();
        $cat  = $this->makeExpenseCategory($user);
        $budget = Budget::factory()->create([
            'user_id'         => $user->id,
            'category_id'     => $cat->id,
            'monthly_amount'  => 100,
            'alert_threshold' => 80,
        ]);

        Transaction::factory()->for($user)->create([
            'category_id'      => $cat->id,
            'type'             => 'expense',
            'amount'           => 85,
            'transaction_date' => now()->toDateString(),
        ]);

        $this->assertEquals('warn', $budget->alertLevel());
    }

    public function test_alert_level_over_at_full(): void
    {
        $user = $this->makeUser();
        $cat  = $this->makeExpenseCategory($user);
        $budget = Budget::factory()->create([
            'user_id'         => $user->id,
            'category_id'     => $cat->id,
            'monthly_amount'  => 100,
            'alert_threshold' => 80,
        ]);

        Transaction::factory()->for($user)->create([
            'category_id'      => $cat->id,
            'type'             => 'expense',
            'amount'           => 150,
            'transaction_date' => now()->toDateString(),
        ]);

        $this->assertEquals('over', $budget->alertLevel());
    }

    public function test_scope_active_excludes_inactive(): void
    {
        $user = $this->makeUser();
        $cat1 = $this->makeExpenseCategory($user);
        $cat2 = $this->makeExpenseCategory($user);

        Budget::factory()->create(['user_id' => $user->id, 'category_id' => $cat1->id, 'is_active' => true]);
        Budget::factory()->inactive()->create(['user_id' => $user->id, 'category_id' => $cat2->id]);

        $this->assertCount(1, Budget::active()->where('user_id', $user->id)->get());
    }

    // ─────────────────────────────────────────────────────────────
    //  CASCADE
    // ─────────────────────────────────────────────────────────────

    public function test_budget_cascades_on_user_delete(): void
    {
        $user = $this->makeUser();
        $cat  = $this->makeExpenseCategory($user);
        $budget = Budget::factory()->create(['user_id' => $user->id, 'category_id' => $cat->id]);

        $user->delete();

        $this->assertDatabaseMissing('budgets', ['id' => $budget->id]);
    }
}