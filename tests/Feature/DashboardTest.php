<?php

namespace Tests\Feature;

use App\Models\Nasabah;
use App\Models\TransaksiSetor;
use App\Models\TransaksiTarikDana;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_dashboard_loads_successfully(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewIs('dashboard');
        $response->assertSeeText('Total User');
        $response->assertSeeText('Nasabah Terdaftar');
        $response->assertSeeText('Setoran Bulan Ini');
        $response->assertSeeText('Penarikan Pending');
    }

    public function test_admin_dashboard_shows_correct_user_counts(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->count(3)->create(['role' => 'kader']);
        User::factory()->count(5)->create(['role' => 'nasabah']);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk();
        // 1 admin + 3 kader + 5 nasabah = 9 total
        $response->assertViewHas('totalUser', 9);
        $response->assertViewHas('totalAdmin', 1);
        $response->assertViewHas('totalKader', 3);
        $response->assertViewHas('totalNasabah', 5);
    }

    public function test_admin_dashboard_shows_pending_withdrawal_count(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $kader = User::factory()->create(['role' => 'kader']);
        $nasabahUser = User::factory()->create(['role' => 'nasabah']);
        $nasabah = Nasabah::factory()->create(['user_id' => $nasabahUser->id, 'created_by' => $kader->id]);

        TransaksiTarikDana::factory()->count(2)->create(['nasabah_id' => $nasabah->id, 'status' => 'pending']);
        TransaksiTarikDana::factory()->count(1)->create(['nasabah_id' => $nasabah->id, 'status' => 'selesai']);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewHas('penarikanPending', 2);
    }

    public function test_kader_dashboard_loads_successfully(): void
    {
        $kader = User::factory()->create(['role' => 'kader']);

        $response = $this->actingAs($kader)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewIs('dashboard');
        $response->assertSeeText('Nasabah Dibina');
        $response->assertSeeText('Setoran Bulan Ini');
    }

    public function test_kader_dashboard_shows_only_their_data(): void
    {
        $kader = User::factory()->create(['role' => 'kader']);
        $otherKader = User::factory()->create(['role' => 'kader']);

        $nasabahUser1 = User::factory()->create(['role' => 'nasabah']);
        $nasabahUser2 = User::factory()->create(['role' => 'nasabah']);

        $nasabah1 = Nasabah::factory()->create(['user_id' => $nasabahUser1->id, 'created_by' => $kader->id]);
        $nasabah2 = Nasabah::factory()->create(['user_id' => $nasabahUser2->id, 'created_by' => $otherKader->id]);

        TransaksiSetor::factory()->count(3)->create(['kader_id' => $kader->id, 'nasabah_id' => $nasabah1->id]);
        TransaksiSetor::factory()->count(2)->create(['kader_id' => $otherKader->id, 'nasabah_id' => $nasabah2->id]);

        $response = $this->actingAs($kader)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewHas('totalNasabahDibina', 1);
        $response->assertViewHas('totalSetoran', 3);
    }

    public function test_nasabah_dashboard_loads_successfully(): void
    {
        $nasabahUser = User::factory()->create(['role' => 'nasabah']);
        $kader = User::factory()->create(['role' => 'kader']);
        Nasabah::factory()->create(['user_id' => $nasabahUser->id, 'created_by' => $kader->id]);

        $response = $this->actingAs($nasabahUser)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewIs('dashboard');
        $response->assertSeeText('Saldo Aktif');
        $response->assertSeeText('Total Setoran');
    }

    public function test_nasabah_without_profile_sees_warning_message(): void
    {
        $nasabahUser = User::factory()->create(['role' => 'nasabah']);

        $response = $this->actingAs($nasabahUser)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSeeText('Profil nasabah belum tersedia');
    }

    public function test_nasabah_dashboard_shows_correct_saldo(): void
    {
        $nasabahUser = User::factory()->create(['role' => 'nasabah']);
        $kader = User::factory()->create(['role' => 'kader']);
        Nasabah::factory()->create([
            'user_id' => $nasabahUser->id,
            'created_by' => $kader->id,
            'saldo_aktif' => 75000,
        ]);

        $response = $this->actingAs($nasabahUser)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewHas('saldoAktif', 75000.0);
    }
}
