<?php

namespace Tests\Feature;

use App\Models\Nasabah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNasabahCrudTest extends TestCase
{
    use RefreshDatabase;

    // ========================
    // Web Routes (Blade Views)
    // ========================

    public function test_admin_can_view_nasabah_index(): void
    {
        $admin = User::factory()->admin()->create();
        Nasabah::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get(route('admin.nasabah.index'));

        $response->assertStatus(200)
            ->assertViewIs('admin.nasabah.index')
            ->assertViewHas('nasabahList');
    }

    public function test_non_admin_cannot_view_nasabah_index(): void
    {
        $kader = User::factory()->kader()->create();

        $response = $this->actingAs($kader)->get(route('admin.nasabah.index'));

        $response->assertStatus(403);
    }

    public function test_guest_is_redirected_from_nasabah_admin_pages(): void
    {
        $response = $this->get(route('admin.nasabah.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_view_nasabah_create_form(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.nasabah.create'));

        $response->assertStatus(200)
            ->assertViewIs('admin.nasabah.create');
    }

    public function test_admin_can_create_new_nasabah_with_auto_credentials(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.nasabah.store'), [
            'nik' => '3201011212900001',
            'nama' => 'Budi Santoso',
            'no_hp' => '081234567890',
            'nomor_rw' => '05',
            'nomor_rt' => '02',
            'alamat' => 'Jl. Merak No. 10',
            'saldo_awal' => 50000,
        ]);

        $response->assertRedirect(route('admin.nasabah.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('nasabah', [
            'nik' => '3201011212900001',
            'nama' => 'Budi Santoso',
            'nomor_rw' => '05',
            'saldo_aktif' => 50000.00,
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Budi Santoso',
            'role' => 'nasabah',
        ]);
    }

    public function test_store_nasabah_validates_required_fields(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.nasabah.store'), []);

        $response->assertStatus(302)
            ->assertSessionHasErrors(['nik', 'nama', 'no_hp', 'nomor_rw']);
    }

    public function test_store_nasabah_validates_unique_nik(): void
    {
        $admin = User::factory()->admin()->create();
        Nasabah::factory()->create(['nik' => '3201011212900001']);

        $response = $this->actingAs($admin)->post(route('admin.nasabah.store'), [
            'nik' => '3201011212900001',
            'nama' => 'Nasabah Duplikat',
            'no_hp' => '081299998888',
            'nomor_rw' => '01',
        ]);

        $response->assertStatus(302)
            ->assertSessionHasErrors(['nik']);
    }

    public function test_admin_can_view_nasabah_detail(): void
    {
        $admin = User::factory()->admin()->create();
        $nasabah = Nasabah::factory()->create();

        $response = $this->actingAs($admin)->get(route('admin.nasabah.show', $nasabah));

        $response->assertStatus(200)
            ->assertViewIs('admin.nasabah.show')
            ->assertViewHas('nasabah');
    }

    public function test_admin_can_view_nasabah_edit_form(): void
    {
        $admin = User::factory()->admin()->create();
        $nasabah = Nasabah::factory()->create();

        $response = $this->actingAs($admin)->get(route('admin.nasabah.edit', $nasabah));

        $response->assertStatus(200)
            ->assertViewIs('admin.nasabah.edit');
    }

    public function test_admin_can_update_nasabah(): void
    {
        $admin = User::factory()->admin()->create();
        $nasabah = Nasabah::factory()->create();

        $response = $this->actingAs($admin)->put(route('admin.nasabah.update', $nasabah), [
            'nik' => $nasabah->nik,
            'nama' => 'Nama Nasabah Baru',
            'no_hp' => '089999999999',
            'nomor_rw' => '08',
            'nomor_rt' => '04',
            'alamat' => 'Alamat baru nasabah',
            'username' => $nasabah->user->username,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.nasabah.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('nasabah', [
            'id' => $nasabah->id,
            'nama' => 'Nama Nasabah Baru',
            'nomor_rw' => '08',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $nasabah->user_id,
            'name' => 'Nama Nasabah Baru',
        ]);
    }

    public function test_admin_can_delete_nasabah(): void
    {
        $admin = User::factory()->admin()->create();
        $nasabah = Nasabah::factory()->create();
        $userId = $nasabah->user_id;

        $response = $this->actingAs($admin)->delete(route('admin.nasabah.destroy', $nasabah));

        $response->assertRedirect(route('admin.nasabah.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('nasabah', ['id' => $nasabah->id]);
        $this->assertDatabaseMissing('users', ['id' => $userId]);
    }

    // ========================
    // REST API Routes (Sanctum)
    // ========================

    public function test_api_admin_can_list_nasabah(): void
    {
        $admin = User::factory()->admin()->create();
        Nasabah::factory()->count(3)->create();

        $response = $this->actingAs($admin)->getJson('/api/admin/nasabah');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['data']]);
    }

    public function test_api_non_admin_cannot_list_nasabah(): void
    {
        $kader = User::factory()->kader()->create();

        $response = $this->actingAs($kader)->getJson('/api/admin/nasabah');

        $response->assertStatus(403)
            ->assertJsonPath('success', false);
    }

    public function test_api_unauthenticated_cannot_list_nasabah(): void
    {
        $response = $this->getJson('/api/admin/nasabah');

        $response->assertStatus(401);
    }

    public function test_api_admin_can_create_nasabah(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->postJson('/api/admin/nasabah', [
            'nik' => '3201019999990001',
            'nama' => 'Nasabah API',
            'no_hp' => '081288887777',
            'nomor_rw' => '03',
            'nomor_rt' => '01',
            'saldo_awal' => 20000,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.nasabah.nik', '3201019999990001');
    }

    public function test_api_admin_can_get_nasabah_detail(): void
    {
        $admin = User::factory()->admin()->create();
        $nasabah = Nasabah::factory()->create();

        $response = $this->actingAs($admin)->getJson("/api/admin/nasabah/{$nasabah->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $nasabah->id);
    }

    public function test_api_admin_can_update_nasabah(): void
    {
        $admin = User::factory()->admin()->create();
        $nasabah = Nasabah::factory()->create();

        $response = $this->actingAs($admin)->putJson("/api/admin/nasabah/{$nasabah->id}", [
            'nik' => $nasabah->nik,
            'nama' => 'Nasabah API Update',
            'no_hp' => '087777777777',
            'nomor_rw' => '09',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.nama', 'Nasabah API Update');
    }

    public function test_api_admin_can_delete_nasabah(): void
    {
        $admin = User::factory()->admin()->create();
        $nasabah = Nasabah::factory()->create();
        $userId = $nasabah->user_id;

        $response = $this->actingAs($admin)->deleteJson("/api/admin/nasabah/{$nasabah->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('nasabah', ['id' => $nasabah->id]);
        $this->assertDatabaseMissing('users', ['id' => $userId]);
    }
}
