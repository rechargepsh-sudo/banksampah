<?php

namespace Tests\Feature;

use App\Models\Nasabah;
use App\Models\TransaksiTarikDana;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PenarikanDanaTest extends TestCase
{
    use RefreshDatabase;

    public function test_kader_can_approve_withdrawal_deducting_balance_and_creating_mutation(): void
    {
        $kader = User::factory()->kader()->create();
        Sanctum::actingAs($kader);

        $nasabah = Nasabah::factory()->create([
            'saldo_aktif' => 50000.00,
        ]);

        $penarikan = TransaksiTarikDana::factory()->create([
            'nasabah_id' => $nasabah->id,
            'jumlah_penarikan' => 20000.00,
            'metode_penarikan' => 'tunai',
            'status' => 'pending',
        ]);

        $response = $this->postJson("/api/kader/penarikan/{$penarikan->id}/validasi", [
            'status' => 'selesai',
            'catatan' => 'Uang tunai Rp 20.000 telah diserahkan langsung',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'selesai')
            ->assertJsonPath('data.diproses_oleh', $kader->name)
            ->assertJsonPath('data.nasabah.saldo_aktif_terbaru', 30000);

        // Verifikasi database penarikan
        $this->assertDatabaseHas('transaksi_tarik_dana', [
            'id' => $penarikan->id,
            'status' => 'selesai',
            'diproses_oleh' => $kader->id,
        ]);

        // Verifikasi mutasi saldo keluar
        $this->assertDatabaseHas('mutasi_saldo', [
            'nasabah_id' => $nasabah->id,
            'jenis_mutasi' => 'keluar',
            'nominal' => 20000.00,
            'saldo_sebelum' => 50000.00,
            'saldo_sesudah' => 30000.00,
            'referensi_id' => $penarikan->id,
            'tipe_referensi' => 'transaksi_tarik_dana',
        ]);

        // Verifikasi saldo aktif nasabah terpotong
        $nasabah->refresh();
        $this->assertEquals(30000.00, $nasabah->saldo_aktif);
    }

    public function test_kader_can_reject_withdrawal_without_deducting_balance(): void
    {
        $kader = User::factory()->kader()->create();
        Sanctum::actingAs($kader);

        $nasabah = Nasabah::factory()->create([
            'saldo_aktif' => 50000.00,
        ]);

        $penarikan = TransaksiTarikDana::factory()->create([
            'nasabah_id' => $nasabah->id,
            'jumlah_penarikan' => 20000.00,
            'status' => 'pending',
        ]);

        $response = $this->postJson("/api/kader/penarikan/{$penarikan->id}/validasi", [
            'status' => 'ditolak',
            'catatan' => 'Kas tunai sedang kosong, silakan ajukan transfer atau hubungi ketua RW',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'ditolak');

        $this->assertDatabaseHas('transaksi_tarik_dana', [
            'id' => $penarikan->id,
            'status' => 'ditolak',
            'diproses_oleh' => $kader->id,
        ]);

        // Mutasi saldo keluar tidak boleh dibuat
        $this->assertDatabaseMissing('mutasi_saldo', [
            'referensi_id' => $penarikan->id,
            'jenis_mutasi' => 'keluar',
        ]);

        // Saldo nasabah tetap utuh
        $nasabah->refresh();
        $this->assertEquals(50000.00, $nasabah->saldo_aktif);
    }

    public function test_cannot_revalidate_already_completed_withdrawal(): void
    {
        $kader = User::factory()->kader()->create();
        Sanctum::actingAs($kader);

        $penarikan = TransaksiTarikDana::factory()->selesai($kader)->create();

        $response = $this->postJson("/api/kader/penarikan/{$penarikan->id}/validasi", [
            'status' => 'selesai',
        ]);

        $response->assertStatus(400)
            ->assertJsonPath('success', false);
    }

    public function test_cannot_approve_if_nasabah_balance_is_now_insufficient(): void
    {
        $kader = User::factory()->kader()->create();
        Sanctum::actingAs($kader);

        $nasabah = Nasabah::factory()->create([
            'saldo_aktif' => 5000.00, // Saldo berkurang di bawah jumlah penarikan
        ]);

        $penarikan = TransaksiTarikDana::factory()->create([
            'nasabah_id' => $nasabah->id,
            'jumlah_penarikan' => 20000.00,
            'status' => 'pending',
        ]);

        $response = $this->postJson("/api/kader/penarikan/{$penarikan->id}/validasi", [
            'status' => 'selesai',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    public function test_nasabah_cannot_validate_withdrawal(): void
    {
        $nasabahUser = User::factory()->nasabah()->create();
        Sanctum::actingAs($nasabahUser);

        $penarikan = TransaksiTarikDana::factory()->create(['status' => 'pending']);

        $response = $this->postJson("/api/kader/penarikan/{$penarikan->id}/validasi", [
            'status' => 'selesai',
        ]);

        $response->assertStatus(403);
    }
}
