<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\RecurringTransaction;
use App\Models\Transaction;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecurringTransactionTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(): User
    {
        return User::factory()->create();
    }

    private function makeRecurring(?User $user = null, array $overrides = []): RecurringTransaction
    {
        $user ??= $this->makeUser();
        $category = Category::factory()->for($user)->create([
            'type' => $overrides['type'] ?? 'expense',
        ]);
        return RecurringTransaction::factory()->create(array_merge([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'amount' => 50,
            'frequency' => 'monthly',
            'interval' => 1,
            'start_date' => CarbonImmutable::now()->startOfMonth()->toDateString(),
            'next_occurrence' => CarbonImmutable::now()->startOfMonth()->toDateString(),
        ], $overrides));
    }

    // ─────────────────────────────────────────────────────────────
    //  AUTHORIZATION
    // ─────────────────────────────────────────────────────────────

    public function test_guest_cannot_view_recurring(): void
    {
        $this->get('/recurring')->assertRedirect('/login');
    }

    public function test_user_cannot_view_other_users_recurring(): void
    {
        $user  = $this->makeUser();
        $other = $this->makeUser();
        $r = $this->makeRecurring($other);

        $this->actingAs($user)
            ->get(route('recurring.edit', $r))
            ->assertForbidden();

        $this->actingAs($user)
            ->post(route('recurring.post', $r))
            ->assertForbidden();

        $this->actingAs($user)
            ->delete(route('recurring.destroy', $r))
            ->assertForbidden();
    }

    // ─────────────────────────────────────────────────────────────
    //  CRUD
    // ─────────────────────────────────────────────────────────────

    public function test_user_can_create_recurring(): void
    {
        $user = $this->makeUser();
        $cat = Category::factory()->for($user)->create(['type' => 'expense']);

        $this->actingAs($user)
            ->post('/recurring', [
                'category_id' => $cat->id,
                'type' => 'expense',
                'amount' => 99.99,
                'description' => 'Netflix',
                'frequency' => 'monthly',
                'interval' => 1,
                'start_date' => '2026-06-01',
                'is_active' => 1,
            ])
            ->assertRedirect(route('recurring.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('recurring_transactions', [
            'user_id' => $user->id,
            'category_id' => $cat->id,
            'amount' => '99.99',
            'description' => 'Netflix',
            'frequency' => 'monthly',
            'next_occurrence' => '2026-06-01',
        ]);
    }

    public function test_validates_required_fields(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
            ->post('/recurring', [])
            ->assertSessionHasErrors(['category_id', 'type', 'amount', 'frequency', 'interval', 'start_date']);
    }

    public function test_validates_frequency_in_set(): void
    {
        $user = $this->makeUser();
        $cat = Category::factory()->for($user)->create(['type' => 'expense']);

        $this->actingAs($user)
            ->post('/recurring', [
                'category_id' => $cat->id, 'type' => 'expense', 'amount' => 50,
                'frequency' => 'bimensual', 'interval' => 1, 'start_date' => '2026-06-01',
            ])
            ->assertSessionHasErrors('frequency');
    }

    public function test_validates_end_date_after_start_date(): void
    {
        $user = $this->makeUser();
        $cat = Category::factory()->for($user)->create(['type' => 'expense']);

        $this->actingAs($user)
            ->post('/recurring', [
                'category_id' => $cat->id, 'type' => 'expense', 'amount' => 50,
                'frequency' => 'monthly', 'interval' => 1,
                'start_date' => '2026-06-01', 'end_date' => '2026-05-01',
            ])
            ->assertSessionHasErrors('end_date');
    }

    public function test_validates_category_belongs_to_user(): void
    {
        $user = $this->makeUser();
        $other = $this->makeUser();
        $otherCat = Category::factory()->for($other)->create(['type' => 'expense']);

        $this->actingAs($user)
            ->post('/recurring', [
                'category_id' => $otherCat->id, 'type' => 'expense', 'amount' => 50,
                'frequency' => 'monthly', 'interval' => 1, 'start_date' => '2026-06-01',
            ])
            ->assertSessionHasErrors('category_id');
    }

    public function test_user_can_update_recurring(): void
    {
        $user = $this->makeUser();
        $r = $this->makeRecurring($user, ['amount' => 50]);

        $this->actingAs($user)
            ->put(route('recurring.update', $r), [
                'category_id' => $r->category_id,
                'type' => $r->type,
                'amount' => 99,
                'frequency' => 'monthly',
                'interval' => 1,
                'start_date' => $r->start_date->toDateString(),
                'is_active' => 1,
            ])
            ->assertRedirect(route('recurring.index'));

        $r->refresh();
        $this->assertEquals('99.00', (string) $r->amount);
    }

    public function test_user_can_delete_recurring(): void
    {
        $user = $this->makeUser();
        $r = $this->makeRecurring($user);

        $this->actingAs($user)
            ->delete(route('recurring.destroy', $r))
            ->assertRedirect(route('recurring.index'));

        $this->assertDatabaseMissing('recurring_transactions', ['id' => $r->id]);
    }

    // ─────────────────────────────────────────────────────────────
    //  CALCULATION
    // ─────────────────────────────────────────────────────────────

    public function test_advance_occurrence_monthly(): void
    {
        $r = $this->makeRecurring(null, [
            'frequency' => 'monthly', 'interval' => 1,
            'next_occurrence' => '2026-01-31',
        ]);
        $next = $r->advanceOccurrence('2026-01-31');
        $this->assertSame('2026-02-28', $next->toDateString());
    }

    public function test_advance_occurrence_weekly(): void
    {
        $r = $this->makeRecurring(null, [
            'frequency' => 'weekly', 'interval' => 2,
            'next_occurrence' => '2026-06-01',
        ]);
        $next = $r->advanceOccurrence('2026-06-01');
        $this->assertSame('2026-06-15', $next->toDateString());
    }

    public function test_advance_occurrence_yearly(): void
    {
        $r = $this->makeRecurring(null, [
            'frequency' => 'yearly', 'interval' => 1,
            'next_occurrence' => '2024-02-29',
        ]);
        $next = $r->advanceOccurrence('2024-02-29');
        $this->assertSame('2025-02-28', $next->toDateString());
    }

    public function test_post_now_creates_transaction_and_advances(): void
    {
        CarbonImmutable::setTestNow('2026-06-15');
        $user = $this->makeUser();
        $r = $this->makeRecurring($user, [
            'amount' => 50,
            'next_occurrence' => '2026-06-01',
            'description' => 'Suscripción',
        ]);

        $tx = $r->postNow();

        $this->assertNotNull($tx);
        $this->assertEquals(50.0, (float) $tx->amount);
        $this->assertEquals('Suscripción', $tx->description);
        $this->assertEquals($user->id, $tx->user_id);
        $this->assertEquals('expense', $tx->type);
        $this->assertEquals('2026-06-01', $tx->transaction_date->toDateString());

        $r->refresh();
        $this->assertEquals('2026-07-01', $r->next_occurrence->toDateString());
        $this->assertEquals('2026-06-15', $r->last_posted_at->toDateString());

        CarbonImmutable::setTestNow();
    }

    public function test_post_now_deactivates_when_past_end_date(): void
    {
        CarbonImmutable::setTestNow('2026-12-15');
        $user = $this->makeUser();
        $r = $this->makeRecurring($user, [
            'end_date' => '2026-06-30',
            'next_occurrence' => '2026-06-01',
        ]);

        $tx = $r->postNow();

        $this->assertNull($tx);
        $r->refresh();
        $this->assertFalse($r->is_active);

        CarbonImmutable::setTestNow();
    }

    public function test_post_now_returns_null_when_inactive(): void
    {
        $user = $this->makeUser();
        $r = $this->makeRecurring($user, ['is_active' => false]);

        $this->assertNull($r->postNow());
    }

    public function test_due_scope_returns_only_due_active(): void
    {
        $user = $this->makeUser();
        $this->makeRecurring($user, ['next_occurrence' => '2020-01-01']); // due
        $this->makeRecurring($user, ['next_occurrence' => now()->addDays(7)->toDateString()]); // future
        $rInactive = $this->makeRecurring($user, ['next_occurrence' => '2020-01-01']);
        $rInactive->update(['is_active' => false]);

        $this->assertCount(1, RecurringTransaction::due()->where('user_id', $user->id)->get());
    }

    public function test_is_due_helper(): void
    {
        $user = $this->makeUser();
        $past = $this->makeRecurring($user, ['next_occurrence' => '2020-01-01']);
        $future = $this->makeRecurring($user, ['next_occurrence' => now()->addDays(7)->toDateString()]);

        $this->assertTrue($past->isDue());
        $this->assertFalse($future->isDue());
    }

    public function test_post_endpoint_creates_transaction(): void
    {
        CarbonImmutable::setTestNow('2026-06-15');
        $user = $this->makeUser();
        $r = $this->makeRecurring($user, ['amount' => 75, 'next_occurrence' => '2026-06-01']);

        $this->actingAs($user)
            ->post(route('recurring.post', $r))
            ->assertRedirect();

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'amount' => '75.00',
            'type' => 'expense',
            'transaction_date' => '2026-06-01',
        ]);

        CarbonImmutable::setTestNow();
    }

    public function test_cascade_on_user_delete(): void
    {
        $user = $this->makeUser();
        $r = $this->makeRecurring($user);

        $user->delete();

        $this->assertDatabaseMissing('recurring_transactions', ['id' => $r->id]);
    }
}