<?php

namespace Tests\Feature;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\BudgetAlertNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(): User
    {
        return User::factory()->create();
    }

    private function makeBudget(User $user, float $monthly = 100, int $threshold = 80, bool $active = true): array
    {
        $category = Category::factory()->for($user)->create(['type' => 'expense']);
        $budget = Budget::factory()->create([
            'user_id'         => $user->id,
            'category_id'     => $category->id,
            'monthly_amount'  => $monthly,
            'alert_threshold' => $threshold,
            'is_active'       => $active,
        ]);
        return [$category, $budget];
    }

    public function test_transaction_at_threshold_creates_notification(): void
    {
        Carbon::setTestNow('2026-06-15');

        $user = User::factory()->create();
        [$category, $budget] = $this->makeBudget($user, monthly: 100, threshold: 80);

        // 85 → supera 80% → debe notificar
        Transaction::factory()->for($user)->create([
            'category_id'      => $category->id,
            'type'             => 'expense',
            'amount'           => 85,
            'transaction_date' => '2026-06-15',
        ]);

        $this->assertDatabaseCount('notifications', 1);
        $n = $user->notifications()->first();
        $this->assertEquals(BudgetAlertNotification::class, $n->type);
        $this->assertEquals('warn', $n->data['level']);
        $this->assertEquals($budget->id, $n->data['budget_id']);
        $this->assertEquals('2026-06', $n->data['period']);

        Carbon::setTestNow();
    }

    public function test_transaction_under_threshold_does_not_notify(): void
    {
        Carbon::setTestNow('2026-06-15');

        $user = User::factory()->create();
        [$category, $budget] = $this->makeBudget($user, monthly: 100, threshold: 80);

        Transaction::factory()->for($user)->create([
            'category_id'      => $category->id,
            'type'             => 'expense',
            'amount'           => 50, // 50%
            'transaction_date' => '2026-06-15',
        ]);

        $this->assertDatabaseCount('notifications', 0);

        Carbon::setTestNow();
    }

    public function test_over_budget_creates_over_notification(): void
    {
        Carbon::setTestNow('2026-06-15');

        $user = User::factory()->create();
        [$category, $budget] = $this->makeBudget($user, monthly: 100, threshold: 80);

        Transaction::factory()->for($user)->create([
            'category_id'      => $category->id,
            'type'             => 'expense',
            'amount'           => 150, // 150% → over
            'transaction_date' => '2026-06-15',
        ]);

        $n = $user->notifications()->first();
        $this->assertEquals('over', $n->data['level']);

        Carbon::setTestNow();
    }

    public function test_notification_is_idempotent_in_same_period(): void
    {
        Carbon::setTestNow('2026-06-15');

        $user = User::factory()->create();
        [$category, $budget] = $this->makeBudget($user, monthly: 100, threshold: 80);

        Transaction::factory()->for($user)->create([
            'category_id'      => $category->id,
            'type'             => 'expense',
            'amount'           => 85,
            'transaction_date' => '2026-06-15',
        ]);
        Transaction::factory()->for($user)->create([
            'category_id'      => $category->id,
            'type'             => 'expense',
            'amount'           => 10,
            'transaction_date' => '2026-06-16',
        ]);
        Transaction::factory()->for($user)->create([
            'category_id'      => $category->id,
            'type'             => 'expense',
            'amount'           => 5,
            'transaction_date' => '2026-06-20',
        ]);

        // Solo debe existir 1 notificación pese a 3 transacciones en warn
        $this->assertDatabaseCount('notifications', 1);

        Carbon::setTestNow();
    }

    public function test_new_period_creates_new_notification(): void
    {
        $user = User::factory()->create();
        [$category, $budget] = $this->makeBudget($user, monthly: 100, threshold: 80);

        // Junio
        Carbon::setTestNow('2026-06-15');
        Transaction::factory()->for($user)->create([
            'category_id' => $category->id, 'type' => 'expense',
            'amount' => 85, 'transaction_date' => '2026-06-15',
        ]);

        // Julio
        Carbon::setTestNow('2026-07-15');
        Transaction::factory()->for($user)->create([
            'category_id' => $category->id, 'type' => 'expense',
            'amount' => 85, 'transaction_date' => '2026-07-15',
        ]);

        $this->assertDatabaseCount('notifications', 2);
        $periods = $user->notifications()->pluck('data')->map(fn ($d) => $d['period'])->toArray();
        $this->assertEqualsCanonicalizing(['2026-06', '2026-07'], $periods);

        Carbon::setTestNow();
    }

    public function test_income_transaction_does_not_trigger(): void
    {
        Carbon::setTestNow('2026-06-15');
        $user = User::factory()->create();
        [$category, $budget] = $this->makeBudget($user, monthly: 100, threshold: 80);

        Transaction::factory()->for($user)->create([
            'category_id'      => $category->id,
            'type'             => 'income',
            'amount'           => 5000,
            'transaction_date' => '2026-06-15',
        ]);

        $this->assertDatabaseCount('notifications', 0);

        Carbon::setTestNow();
    }

    public function test_inactive_budget_does_not_trigger(): void
    {
        Carbon::setTestNow('2026-06-15');
        $user = User::factory()->create();
        [$category, $budget] = $this->makeBudget($user, monthly: 100, threshold: 80, active: false);

        Transaction::factory()->for($user)->create([
            'category_id'      => $category->id,
            'type'             => 'expense',
            'amount'           => 150,
            'transaction_date' => '2026-06-15',
        ]);

        $this->assertDatabaseCount('notifications', 0);

        Carbon::setTestNow();
    }

    public function test_other_user_transaction_does_not_notify_me(): void
    {
        Carbon::setTestNow('2026-06-15');
        $user  = User::factory()->create();
        $other = User::factory()->create();

        $category = Category::factory()->for($user)->create(['type' => 'expense']);
        $budget = Budget::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'monthly_amount' => 100,
            'alert_threshold' => 80,
        ]);

        // Transacción de OTRO usuario en una categoría con mismo nombre (id distinto)
        $otherCat = Category::factory()->for($other)->create(['type' => 'expense']);
        Transaction::factory()->for($other)->create([
            'category_id'      => $otherCat->id,
            'type'             => 'expense',
            'amount'           => 200,
            'transaction_date' => '2026-06-15',
        ]);

        $this->assertEquals(0, $user->notifications()->count());

        Carbon::setTestNow();
    }

    // ─────────────────────────────────────────────────────────────
    //  Notification Controller
    // ─────────────────────────────────────────────────────────────

    public function test_user_can_view_notifications_index(): void
    {
        $user = $this->makeUser();
        $this->actingAs($user)->get('/notifications')
            ->assertOk()
            ->assertSee('Notificaciones');
    }

    public function test_guest_cannot_view_notifications(): void
    {
        $this->get('/notifications')->assertRedirect('/login');
    }

    public function test_mark_as_read_marks_one(): void
    {
        $user = $this->makeUser();
        $n = $user->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'type' => BudgetAlertNotification::class,
            'data' => ['level' => 'warn', 'budget_id' => 1, 'period' => '2026-06'],
        ]);

        $this->actingAs($user)
            ->patch("/notifications/{$n->id}/read")
            ->assertRedirect();

        $this->assertNotNull($n->fresh()->read_at);
    }

    public function test_user_cannot_mark_others_notification(): void
    {
        $user  = $this->makeUser();
        $other = $this->makeUser();
        $n = $other->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'type' => BudgetAlertNotification::class,
            'data' => [],
        ]);

        $this->actingAs($user)
            ->patch("/notifications/{$n->id}/read")
            ->assertNotFound();
    }

    public function test_mark_all_read_marks_everything(): void
    {
        $user = $this->makeUser();
        foreach (range(1, 3) as $i) {
            $user->notifications()->create([
                'id' => \Illuminate\Support\Str::uuid()->toString(),
                'type' => BudgetAlertNotification::class,
                'data' => ['period' => "2026-06"],
            ]);
        }

        $this->assertEquals(3, $user->unreadNotifications()->count());

        $this->actingAs($user)
            ->post('/notifications/mark-all-read')
            ->assertRedirect();

        $this->assertEquals(0, $user->unreadNotifications()->count());
    }

    public function test_filter_unread_only_shows_unread(): void
    {
        $user = $this->makeUser();

        $user->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'type' => BudgetAlertNotification::class,
            'data' => [
                'level' => 'warn',
                'title' => 'NOTIFICACION_UNREAD_TITLE',
                'body'  => 'Cuerpo de la no leída',
                'budget_id' => 1,
                'period' => '2026-06',
            ],
        ]);
        $user->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'type' => BudgetAlertNotification::class,
            'data' => [
                'level' => 'over',
                'title' => 'NOTIFICACION_READ_TITLE',
                'body'  => 'Cuerpo de la leída',
                'budget_id' => 1,
                'period' => '2026-05',
            ],
            'read_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/notifications?filter=unread')->assertOk();
        $response->assertSee('NOTIFICACION_UNREAD_TITLE', false);
        $response->assertDontSee('NOTIFICACION_READ_TITLE', false);
    }
}