<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserCrudTest extends TestCase
{
    use RefreshDatabase;

    // ========================
    // Web Routes (Blade Views)
    // ========================

    public function test_admin_can_view_user_index(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.users.index'));

        $response->assertStatus(200)
            ->assertViewIs('admin.users.index')
            ->assertViewHas('users');
    }

    public function test_non_admin_cannot_view_user_index(): void
    {
        $kader = User::factory()->kader()->create();

        $response = $this->actingAs($kader)->get(route('admin.users.index'));

        $response->assertStatus(403);
    }

    public function test_guest_is_redirected_from_web_admin_pages(): void
    {
        // Web guard: guest must be redirected to login
        $response = $this->get(route('admin.users.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_view_create_form(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.users.create'));

        $response->assertStatus(200)
            ->assertViewIs('admin.users.create');
    }

    public function test_admin_can_create_new_user(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'User Baru',
            'username' => 'user_baru_test',
            'email' => 'userbaru@example.com',
            'password' => 'password123',
            'role' => 'kader',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'username' => 'user_baru_test',
            'email' => 'userbaru@example.com',
            'role' => 'kader',
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.users.store'), []);

        $response->assertStatus(302)
            ->assertSessionHasErrors(['name', 'username', 'password', 'role']);
    }

    public function test_store_validates_duplicate_username(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->create(['username' => 'taken_username']);

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Test User',
            'username' => 'taken_username',
            'password' => 'password123',
            'role' => 'kader',
        ]);

        $response->assertStatus(302)
            ->assertSessionHasErrors(['username']);
    }

    public function test_admin_can_view_user_detail(): void
    {
        $admin = User::factory()->admin()->create();
        $targetUser = User::factory()->kader()->create();

        $response = $this->actingAs($admin)->get(route('admin.users.show', $targetUser));

        $response->assertStatus(200)
            ->assertViewIs('admin.users.show')
            ->assertViewHas('user');
    }

    public function test_admin_can_view_edit_form(): void
    {
        $admin = User::factory()->admin()->create();
        $targetUser = User::factory()->kader()->create();

        $response = $this->actingAs($admin)->get(route('admin.users.edit', $targetUser));

        $response->assertStatus(200)
            ->assertViewIs('admin.users.edit');
    }

    public function test_admin_can_update_user(): void
    {
        $admin = User::factory()->admin()->create();
        $targetUser = User::factory()->kader()->create();

        $response = $this->actingAs($admin)->put(route('admin.users.update', $targetUser), [
            'name' => 'Nama Diperbarui',
            'username' => $targetUser->username,
            'email' => $targetUser->email,
            'role' => 'nasabah',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'name' => 'Nama Diperbarui',
            'role' => 'nasabah',
        ]);
    }

    public function test_admin_can_delete_other_user(): void
    {
        $admin = User::factory()->admin()->create();
        $targetUser = User::factory()->kader()->create();

        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $targetUser));

        $response->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $targetUser->id]);
    }

    public function test_admin_cannot_delete_their_own_account(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $admin));

        $response->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_cannot_demote_own_role(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->put(route('admin.users.update', $admin), [
            'name' => $admin->name,
            'username' => $admin->username,
            'role' => 'kader',
            'is_active' => '1',
        ]);

        $response->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'role' => 'admin',
        ]);
    }

    // ========================
    // REST API Routes (Sanctum)
    // ========================

    public function test_api_admin_can_list_users(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->count(5)->create();

        $response = $this->actingAs($admin)->getJson('/api/admin/users');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['data']]);
    }

    public function test_api_non_admin_cannot_list_users(): void
    {
        $kader = User::factory()->kader()->create();

        $response = $this->actingAs($kader)->getJson('/api/admin/users');

        $response->assertStatus(403)
            ->assertJsonPath('success', false);
    }

    public function test_api_unauthenticated_cannot_list_users(): void
    {
        $response = $this->getJson('/api/admin/users');

        $response->assertStatus(401);
    }

    public function test_api_admin_can_create_user(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->postJson('/api/admin/users', [
            'name' => 'User API Baru',
            'username' => 'user_api_baru',
            'email' => 'apibaru@example.com',
            'password' => 'password123',
            'role' => 'kader',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.username', 'user_api_baru');
    }

    public function test_api_admin_can_get_user_detail(): void
    {
        $admin = User::factory()->admin()->create();
        $targetUser = User::factory()->kader()->create();

        $response = $this->actingAs($admin)->getJson("/api/admin/users/{$targetUser->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $targetUser->id);
    }

    public function test_api_admin_can_update_user(): void
    {
        $admin = User::factory()->admin()->create();
        $targetUser = User::factory()->kader()->create();

        $response = $this->actingAs($admin)->putJson("/api/admin/users/{$targetUser->id}", [
            'name' => 'Diperbarui via API',
            'username' => $targetUser->username,
            'role' => 'nasabah',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Diperbarui via API');
    }

    public function test_api_admin_can_delete_user(): void
    {
        $admin = User::factory()->admin()->create();
        $targetUser = User::factory()->kader()->create();

        $response = $this->actingAs($admin)->deleteJson("/api/admin/users/{$targetUser->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('users', ['id' => $targetUser->id]);
    }

    public function test_api_admin_cannot_delete_own_account(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->deleteJson("/api/admin/users/{$admin->id}");

        $response->assertStatus(422)
            ->assertJsonPath('success', false);

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }
}
