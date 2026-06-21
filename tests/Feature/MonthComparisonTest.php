<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class MonthComparisonTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_monthly_kpis(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/dashboard')->assertOk();
    }

    public function test_monthly_totals_match_current_month_only(): void
    {
        $user = User::factory()->create();
        $now = Carbon::now();

        // Income this month
        Transaction::factory()->for($user)->create([
            'type' => 'income', 'amount' => 1000,
            'transaction_date' => $now->toDateString(),
        ]);
        // Expense this month
        Transaction::factory()->for($user)->create([
            'type' => 'expense', 'amount' => 300,
            'transaction_date' => $now->toDateString(),
        ]);
        // Income last month (must be excluded from MONTHLY income KPI)
        $prevTx = Transaction::factory()->for($user)->create([
            'type' => 'income', 'amount' => 9999,
            'transaction_date' => $now->copy()->subMonthNoOverflow()->toDateString(),
        ]);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertOk()
            ->assertSee('$1,000.00')
            ->assertSee('$300.00');
        // El histórico sí incluye la del mes pasado ($9999 + $1000 - $300 = $10699)
        $response->assertSee('$10,699.00');
        // Sanity: la transacción previa existe
        $this->assertDatabaseHas('transactions', ['id' => $prevTx->id, 'amount' => '9999.00']);
    }

    public function test_previous_month_totals_are_computed(): void
    {
        $user = User::factory()->create();
        $prev = Carbon::now()->subMonthNoOverflow();

        Transaction::factory()->for($user)->create([
            'type' => 'income', 'amount' => 500,
            'transaction_date' => $prev->toDateString(),
        ]);
        Transaction::factory()->for($user)->create([
            'type' => 'expense', 'amount' => 200,
            'transaction_date' => $prev->toDateString(),
        ]);

        $this->actingAs($user)->get('/dashboard')
            ->assertOk()
            ->assertSee('$500.00')
            ->assertSee('$200.00');
    }
}