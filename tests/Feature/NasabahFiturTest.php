<?php

namespace Tests\Feature;

use App\Models\MutasiSaldo;
use App\Models\Nasabah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class NasabahFiturTest extends TestCase
{
    use RefreshDatabase;

    public function test_nasabah_can_check_profile_with_acceptance_criteria(): void
    {
        $user = User::factory()->nasabah()->create();
        $nasabah = Nasabah::factory()->create([
            'user_id' => $user->id,
            'nik' => '3201019988770001',
            'nama' => 'Sri Wahyuni',
            'no_hp' => '081233445566',
            'nomor_rw' => '08',
            'nomor_rt' => '02',
            'alamat' => 'Kampung Hijau No. 20',
            'saldo_aktif' => 75000.00,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/nasabah/profil');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.nik', '3201019988770001')
            ->assertJsonPath('data.nama', 'Sri Wahyuni')
            ->assertJsonPath('data.no_hp', '081233445566')
            ->assertJsonPath('data.nomor_rw', '08')
            ->assertJsonPath('data.nomor_rt', '02')
            ->assertJsonPath('data.saldo_aktif', 75000);
    }

    public function test_nasabah_can_check_saldo_independently(): void
    {
        $user = User::factory()->nasabah()->create();
        $nasabah = Nasabah::factory()->create([
            'user_id' => $user->id,
            'saldo_aktif' => 125000.00,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/nasabah/saldo');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.saldo_aktif', 125000)
            ->assertJsonPath('data.saldo_format', 'Rp 125.000');
    }

    public function test_nasabah_can_check_mutasi_history(): void
    {
        $user = User::factory()->nasabah()->create();
        $nasabah = Nasabah::factory()->create([
            'user_id' => $user->id,
            'saldo_aktif' => 50000.00,
        ]);

        MutasiSaldo::factory()->create([
            'nasabah_id' => $nasabah->id,
            'jenis_mutasi' => 'masuk',
            'nominal' => 30000.00,
        ]);

        MutasiSaldo::factory()->keluar()->create([
            'nasabah_id' => $nasabah->id,
            'nominal' => 10000.00,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/nasabah/mutasi');
        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $data = $response->json('data.data');
        $this->assertCount(2, $data);

        // Filter mutasi jenis keluar
        $responseKeluar = $this->getJson('/api/nasabah/mutasi?jenis_mutasi=keluar');
        $responseKeluar->assertStatus(200);
        $dataKeluar = $responseKeluar->json('data.data');
        $this->assertCount(1, $dataKeluar);
        $this->assertSame('keluar', $dataKeluar[0]['jenis_mutasi']);
    }

    public function test_nasabah_can_request_withdrawal_within_balance(): void
    {
        $user = User::factory()->nasabah()->create();
        $nasabah = Nasabah::factory()->create([
            'user_id' => $user->id,
            'saldo_aktif' => 50000.00,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/nasabah/penarikan', [
            'jumlah_penarikan' => 25000.00,
            'metode_penarikan' => 'tunai',
            'catatan' => 'Mau ambil di pos ronda',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.jumlah_penarikan', 25000);

        $this->assertDatabaseHas('transaksi_tarik_dana', [
            'nasabah_id' => $nasabah->id,
            'jumlah_penarikan' => 25000.00,
            'status' => 'pending',
            'metode_penarikan' => 'tunai',
        ]);
    }

    public function test_nasabah_cannot_request_withdrawal_exceeding_balance(): void
    {
        $user = User::factory()->nasabah()->create();
        $nasabah = Nasabah::factory()->create([
            'user_id' => $user->id,
            'saldo_aktif' => 20000.00,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/nasabah/penarikan', [
            'jumlah_penarikan' => 50000.00,
            'metode_penarikan' => 'transfer',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('saldo_aktif_saat_ini', 20000);

        $this->assertDatabaseMissing('transaksi_tarik_dana', [
            'nasabah_id' => $nasabah->id,
            'jumlah_penarikan' => 50000.00,
        ]);
    }
}
