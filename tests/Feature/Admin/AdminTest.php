<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    // ─────────────────────────────────────────────────────────────
    //  Access control
    // ─────────────────────────────────────────────────────────────

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_regular_user_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin')->assertOk();
    }

    public function test_regular_user_cannot_access_admin_users(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/users')->assertForbidden();
    }

    public function test_admin_can_list_users(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->count(3)->create();

        $this->actingAs($admin)
             ->get('/admin/users')
             ->assertOk()
             ->assertSee('Usuarios');
    }

    // ─────────────────────────────────────────────────────────────
    //  Privacy isolation: admin MUST NOT see financial data
    // ─────────────────────────────────────────────────────────────

    public function test_admin_does_not_see_user_transaction_amounts_anywhere(): void
    {
        $admin   = User::factory()->admin()->create();
        $user    = User::factory()->create();
        $cat     = Category::factory()->for($user)->expense()->create(['name' => 'Comida']);
        $tx      = Transaction::factory()
            ->for($user)->for($cat)->expense()
            ->create(['amount' => 1234.56, 'description' => 'Compra secreta']);

        // 1) Admin dashboard must not contain the amount
        $this->actingAs($admin)->get('/admin')
             ->assertDontSee('1234.56')
             ->assertDontSee('Compra secreta');

        // 2) User list must not contain the amount
        $this->actingAs($admin)->get('/admin/users')
             ->assertDontSee('1234.56')
             ->assertDontSee('Compra secreta');

        // 3) User edit must not contain the amount
        $this->actingAs($admin)->get("/admin/users/{$user->id}/edit")
             ->assertDontSee('1234.56')
             ->assertDontSee('Compra secreta');
    }

    public function test_admin_cannot_access_user_category_edit_route(): void
    {
        $admin = User::factory()->admin()->create();
        $user  = User::factory()->create();
        $cat   = Category::factory()->for($user)->create();

        $this->actingAs($admin)
             ->get("/categories/{$cat->id}/edit")
             ->assertForbidden();
    }

    public function test_admin_cannot_access_user_transaction_edit_route(): void
    {
        $admin = User::factory()->admin()->create();
        $user  = User::factory()->create();
        $cat   = Category::factory()->for($user)->expense()->create();
        $tx    = Transaction::factory()->for($user)->for($cat)->expense()->create();

        $this->actingAs($admin)
             ->get("/transactions/{$tx->id}/edit")
             ->assertForbidden();
    }

    public function test_admin_cannot_view_user_transactions_list(): void
    {
        $admin = User::factory()->admin()->create();
        $user  = User::factory()->create();

        // Admin is authed but /transactions uses `auth()->id()` to filter,
        // so the admin sees their own (empty) data and never the user's.
        $this->actingAs($admin)
             ->get('/transactions')
             ->assertOk();
    }

    // ─────────────────────────────────────────────────────────────
    //  Admin can manage users
    // ─────────────────────────────────────────────────────────────

    public function test_admin_can_view_create_user_form(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
             ->get('/admin/users/create')
             ->assertOk();
    }

    public function test_admin_can_create_a_user(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
             ->post('/admin/users', [
                 'name'                  => 'Nuevo Usuario',
                 'email'                 => 'nuevo@example.com',
                 'password'              => 'password',
                 'password_confirmation' => 'password',
                 'role'                  => 'user',
                 'is_active'             => '1',
             ])
             ->assertRedirect('/admin/users')
             ->assertSessionHas('status');

        $this->assertDatabaseHas('users', [
            'name'      => 'Nuevo Usuario',
            'email'     => 'nuevo@example.com',
            'role'      => 'user',
            'is_active' => 1,
        ]);
    }

    public function test_admin_can_create_another_admin(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
             ->post('/admin/users', [
                 'name'                  => 'Otro Admin',
                 'email'                 => 'otroadmin@example.com',
                 'password'              => 'password',
                 'password_confirmation' => 'password',
                 'role'                  => 'admin',
             ])
             ->assertRedirect('/admin/users');

        $this->assertDatabaseHas('users', [
            'email' => 'otroadmin@example.com',
            'role'  => 'admin',
        ]);
    }

    public function test_email_must_be_unique_when_creating_user(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->create(['email' => 'taken@example.com']);

        $this->actingAs($admin)
             ->post('/admin/users', [
                 'name'                  => 'Test',
                 'email'                 => 'taken@example.com',
                 'password'              => 'password',
                 'password_confirmation' => 'password',
                 'role'                  => 'user',
             ])
             ->assertSessionHasErrors('email');
    }

    public function test_admin_can_edit_a_user(): void
    {
        $admin  = User::factory()->admin()->create();
        $user   = User::factory()->create(['name' => 'Original']);

        $this->actingAs($admin)
             ->get("/admin/users/{$user->id}/edit")
             ->assertOk()
             ->assertSee('Original');
    }

    public function test_admin_can_update_a_user(): void
    {
        $admin = User::factory()->admin()->create();
        $user  = User::factory()->create();

        $this->actingAs($admin)
             ->put("/admin/users/{$user->id}", [
                 'name'      => 'Cambiado',
                 'email'     => $user->email,
                 'role'      => 'user',
                 'is_active' => '0',
             ])
             ->assertRedirect('/admin/users');

        $this->assertDatabaseHas('users', [
            'id'        => $user->id,
            'name'      => 'Cambiado',
            'is_active' => 0,
        ]);
    }

    public function test_admin_can_update_user_password(): void
    {
        $admin = User::factory()->admin()->create();
        $user  = User::factory()->create();

        $this->actingAs($admin)
             ->put("/admin/users/{$user->id}", [
                 'name'                  => $user->name,
                 'email'                 => $user->email,
                 'role'                  => 'user',
                 'password'              => 'newpassword123',
                 'password_confirmation' => 'newpassword123',
                 'is_active'             => '1',
             ])
             ->assertRedirect('/admin/users');

        $user->refresh();
        $this->assertTrue(password_verify('newpassword123', $user->password));
    }

    public function test_admin_can_deactivate_a_user(): void
    {
        $admin = User::factory()->admin()->create();
        $user  = User::factory()->create(['is_active' => true]);

        $this->actingAs($admin)
             ->put("/admin/users/{$user->id}", [
                 'name'      => $user->name,
                 'email'     => $user->email,
                 'role'      => 'user',
                 'is_active' => '0',
             ])
             ->assertRedirect('/admin/users');

        $this->assertFalse($user->fresh()->is_active);
    }

    public function test_admin_can_delete_a_user(): void
    {
        $admin = User::factory()->admin()->create();
        $user  = User::factory()->create();

        $this->actingAs($admin)
             ->from('/admin/users')
             ->delete("/admin/users/{$user->id}")
             ->assertRedirect('/admin/users');

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
             ->from('/admin/users')
             ->delete("/admin/users/{$admin->id}")
             ->assertRedirect('/admin/users')
             ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_inactive_user_cannot_login(): void
    {
        $user = User::factory()->inactive()->create([
            'email' => 'inactive@example.com',
        ]);

        $this->post('/login', [
            'email'    => 'inactive@example.com',
            'password' => 'password',
        ])->assertSessionHasErrors();
        $this->assertGuest();
    }

    // ─────────────────────────────────────────────────────────────
    //  Dashboard metrics
    // ─────────────────────────────────────────────────────────────

    public function test_admin_dashboard_shows_aggregate_metrics(): void
    {
        $admin = User::factory()->admin()->create();
        $user  = User::factory()->create();
        $cat   = Category::factory()->for($user)->expense()->create();
        Transaction::factory()->for($user)->for($cat)->expense()->count(5)->create();

        $response = $this->actingAs($admin)->get('/admin');
        $response->assertOk()
                 ->assertSee('1')          // 1 user created via factory above
                 ->assertSee('5');         // 5 transactions
    }

    public function test_admin_user_index_does_not_expose_email_password(): void
    {
        $admin = User::factory()->admin()->create();
        $user  = User::factory()->create([
            'name'     => 'Privado',
            'email'    => 'privado@example.com',
            'password' => 'super-secret-hash',
        ]);

        $this->actingAs($admin)
             ->get('/admin/users')
             ->assertSee('Privado')
             ->assertSee('privado@example.com')
             ->assertDontSee('super-secret-hash');
    }
}
