<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    // ─────────────────────────────────────────────────────────────
    //  INDEX
    // ─────────────────────────────────────────────────────────────

    public function test_guest_cannot_view_categories(): void
    {
        $this->get('/categories')
             ->assertRedirect('/login');
    }

    public function test_user_can_view_their_categories_index(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->for($user)->create(['name' => 'Salario']);

        $this->actingAs($user)
             ->get('/categories')
             ->assertOk()
             ->assertSee('Salario');
    }

    public function test_index_only_shows_the_authenticated_user_categories(): void
    {
        $user  = User::factory()->create();
        $other = User::factory()->create();

        Category::factory()->for($user)->create(['name' => 'Mía']);
        Category::factory()->for($other)->create(['name' => 'DelOtro']);

        $this->actingAs($user)
             ->get('/categories')
             ->assertSee('Mía')
             ->assertDontSee('DelOtro');
    }

    // ─────────────────────────────────────────────────────────────
    //  CREATE / STORE
    // ─────────────────────────────────────────────────────────────

    public function test_user_can_view_create_form(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
             ->get('/categories/create')
             ->assertOk();
    }

    public function test_user_can_store_a_new_income_category(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
             ->post('/categories', [
                 'name' => 'Salario',
                 'type' => 'income',
             ])
             ->assertRedirect('/categories')
             ->assertSessionHas('status');

        $this->assertDatabaseHas('categories', [
            'user_id' => $user->id,
            'name'    => 'Salario',
            'type'    => 'income',
        ]);
    }

    public function test_user_can_store_a_new_expense_category(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
             ->post('/categories', [
                 'name' => 'Comida',
                 'type' => 'expense',
             ])
             ->assertRedirect('/categories');

        $this->assertDatabaseHas('categories', [
            'user_id' => $user->id,
            'name'    => 'Comida',
            'type'    => 'expense',
        ]);
    }

    public function test_name_is_required(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
             ->post('/categories', [
                 'name' => '',
                 'type' => 'income',
             ])
             ->assertSessionHasErrors('name');

        $this->assertDatabaseCount('categories', 0);
    }

    public function test_type_is_required(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
             ->post('/categories', [
                 'name' => 'Algo',
                 'type' => '',
             ])
             ->assertSessionHasErrors('type');

        $this->assertDatabaseCount('categories', 0);
    }

    public function test_type_must_be_income_or_expense(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
             ->post('/categories', [
                 'name' => 'Algo',
                 'type' => 'invalid',
             ])
             ->assertSessionHasErrors('type');

        $this->assertDatabaseCount('categories', 0);
    }

    public function test_name_must_be_unique_per_user(): void
    {
        $user  = User::factory()->create();
        Category::factory()->for($user)->create(['name' => 'Salario']);

        $this->actingAs($user)
             ->post('/categories', [
                 'name' => 'Salario',
                 'type' => 'income',
             ])
             ->assertSessionHasErrors('name');

        $this->assertDatabaseCount('categories', 1);
    }

    public function test_same_name_can_exist_for_different_users(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        Category::factory()->for($userA)->create(['name' => 'Salario']);

        $this->actingAs($userB)
             ->post('/categories', [
                 'name' => 'Salario',
                 'type' => 'income',
             ])
             ->assertRedirect('/categories');

        $this->assertDatabaseCount('categories', 2);
    }

    public function test_name_must_not_exceed_100_characters(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
             ->post('/categories', [
                 'name' => str_repeat('a', 101),
                 'type' => 'income',
             ])
             ->assertSessionHasErrors('name');
    }

    // ─────────────────────────────────────────────────────────────
    //  EDIT / UPDATE
    // ─────────────────────────────────────────────────────────────

    public function test_user_can_view_edit_form_for_their_category(): void
    {
        $user     = User::factory()->create();
        $category = Category::factory()->for($user)->create();

        $this->actingAs($user)
             ->get("/categories/{$category->id}/edit")
             ->assertOk()
             ->assertSee($category->name);
    }

    public function test_user_cannot_view_edit_form_of_another_users_category(): void
    {
        $owner  = User::factory()->create();
        $stranger = User::factory()->create();
        $category = Category::factory()->for($owner)->create();

        $this->actingAs($stranger)
             ->get("/categories/{$category->id}/edit")
             ->assertForbidden();
    }

    public function test_user_can_update_their_category(): void
    {
        $user     = User::factory()->create();
        $category = Category::factory()->for($user)->create([
            'name' => 'Salario',
            'type' => 'income',
        ]);

        $this->actingAs($user)
             ->put("/categories/{$category->id}", [
                 'name' => 'Nómina',
                 'type' => 'expense',
             ])
             ->assertRedirect('/categories')
             ->assertSessionHas('status');

        $this->assertDatabaseHas('categories', [
            'id'   => $category->id,
            'name' => 'Nómina',
            'type' => 'expense',
        ]);
    }

    public function test_user_cannot_update_another_users_category(): void
    {
        $owner    = User::factory()->create();
        $stranger = User::factory()->create();
        $category = Category::factory()->for($owner)->create(['name' => 'Original']);

        $this->actingAs($stranger)
             ->put("/categories/{$category->id}", [
                 'name' => 'Hackeado',
                 'type' => 'income',
             ])
             ->assertForbidden();

        $this->assertDatabaseHas('categories', [
            'id'   => $category->id,
            'name' => 'Original',
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    //  DESTROY
    // ─────────────────────────────────────────────────────────────

    public function test_user_can_delete_a_category_without_transactions(): void
    {
        $user     = User::factory()->create();
        $category = Category::factory()->for($user)->create();

        $this->actingAs($user)
             ->delete("/categories/{$category->id}")
             ->assertRedirect('/categories')
             ->assertSessionHas('status');

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_user_cannot_delete_a_category_with_transactions(): void
    {
        $user       = User::factory()->create();
        $category   = Category::factory()->for($user)->expense()->create();
        Transaction::factory()->for($user)->for($category)->expense()->create();

        $this->actingAs($user)
             ->from('/categories')
             ->delete("/categories/{$category->id}")
             ->assertRedirect('/categories')
             ->assertSessionHas('error');

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_user_cannot_delete_another_users_category(): void
    {
        $owner    = User::factory()->create();
        $stranger = User::factory()->create();
        $category = Category::factory()->for($owner)->create();

        $this->actingAs($stranger)
             ->delete("/categories/{$category->id}")
             ->assertForbidden();

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }
}
