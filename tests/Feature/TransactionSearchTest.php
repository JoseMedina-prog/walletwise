<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionSearchTest extends TestCase
{
    use RefreshDatabase;

    private function makeTransaction(User $user, string $type, string $description, float $amount = 50.0, ?string $date = null): Transaction
    {
        $category = Category::factory()->for($user)->create(['type' => $type]);
        return Transaction::factory()->for($user)->create([
            'category_id'      => $category->id,
            'type'             => $type,
            'amount'           => $amount,
            'description'      => $description,
            'transaction_date' => $date ?? now()->toDateString(),
        ]);
    }

    public function test_search_filters_by_description_substring(): void
    {
        $user = User::factory()->create();

        $this->makeTransaction($user, 'expense', 'Compra supermercado Mercadona');
        $this->makeTransaction($user, 'expense', 'Cena con amigos');
        $this->makeTransaction($user, 'expense', 'Gasolina Repsol');

        $this->actingAs($user)
            ->get(route('transactions.index', ['q' => 'supermercado']))
            ->assertOk()
            ->assertSee('Compra supermercado Mercadona')
            ->assertDontSee('Cena con amigos')
            ->assertDontSee('Gasolina Repsol');
    }

    public function test_search_is_case_insensitive(): void
    {
        $user = User::factory()->create();
        $this->makeTransaction($user, 'expense', 'Compra en Mercadona');

        $this->actingAs($user)
            ->get(route('transactions.index', ['q' => 'MERCADONA']))
            ->assertOk()
            ->assertSee('Compra en Mercadona');
    }

    public function test_search_preserves_other_filters(): void
    {
        $user = User::factory()->create();

        $incomeCat = Category::factory()->for($user)->create(['type' => 'income']);
        $expenseCat = Category::factory()->for($user)->create(['type' => 'expense']);

        Transaction::factory()->for($user)->create([
            'category_id' => $incomeCat->id,
            'type' => 'income',
            'description' => 'Pago proyecto web',
        ]);
        Transaction::factory()->for($user)->create([
            'category_id' => $expenseCat->id,
            'type' => 'expense',
            'description' => 'Compra proyecto oficina',
        ]);

        $this->actingAs($user)
            ->get(route('transactions.index', ['q' => 'proyecto', 'type' => 'expense']))
            ->assertOk()
            ->assertSee('Compra proyecto oficina')
            ->assertDontSee('Pago proyecto web');
    }

    public function test_search_does_not_leak_other_users_transactions(): void
    {
        $user  = User::factory()->create();
        $other = User::factory()->create();

        $this->makeTransaction($user, 'expense', 'Café con leche');
        $this->makeTransaction($other, 'expense', 'Café con chocolate');

        $this->actingAs($user)
            ->get(route('transactions.index', ['q' => 'café']))
            ->assertOk()
            ->assertSee('Café con leche')
            ->assertDontSee('Café con chocolate');
    }

    public function test_search_returns_empty_state_with_term(): void
    {
        $user = User::factory()->create();
        $this->makeTransaction($user, 'expense', 'Compra supermercado');

        $response = $this->actingAs($user)
            ->get(route('transactions.index', ['q' => 'xyznotfound']));

        $response->assertOk()
            ->assertSee('Sin resultados')
            ->assertSee('xyznotfound');
    }

    public function test_search_escapes_like_wildcards(): void
    {
        $user = User::factory()->create();
        $this->makeTransaction($user, 'expense', '50% descuento zapatería');
        $this->makeTransaction($user, 'expense', 'Pago servicio');

        $this->actingAs($user)
            ->get(route('transactions.index', ['q' => '50%']))
            ->assertOk()
            ->assertSee('50% descuento zapatería')
            ->assertDontSee('Pago servicio');
    }

    public function test_empty_search_returns_all(): void
    {
        $user = User::factory()->create();
        $this->makeTransaction($user, 'expense', 'A');
        $this->makeTransaction($user, 'expense', 'B');

        $this->actingAs($user)
            ->get(route('transactions.index', ['q' => '']))
            ->assertOk()
            ->assertSee('A')
            ->assertSee('B');
    }

    public function test_search_affects_summary_totals(): void
    {
        $user = User::factory()->create();

        $this->makeTransaction($user, 'expense', 'Compra café', 10);
        $this->makeTransaction($user, 'expense', 'Compra cena', 100);

        $response = $this->actingAs($user)
            ->get(route('transactions.index', ['q' => 'café']))
            ->assertOk();

        $response->assertSee('$10.00');
    }
}