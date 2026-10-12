<?php

namespace Tests\Feature;

use App\Models\KategoriSampah;
use App\Models\Nasabah;
use App\Models\TransaksiSetor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TransaksiSetorTest extends TestCase
{
    use RefreshDatabase;

    public function test_kader_can_record_waste_deposit_and_automatically_update_balance_and_mutation(): void
    {
        $kader = User::factory()->kader()->create();
        Sanctum::actingAs($kader);

        $nasabah = Nasabah::factory()->create([
            'saldo_aktif' => 10000.00,
        ]);

        $kardus = KategoriSampah::factory()->create([
            'nama_kategori' => 'Kardus Box',
            'harga_per_kg' => 2500.00,
            'satuan' => 'kg',
            'is_active' => true,
        ]);

        $botol = KategoriSampah::factory()->create([
            'nama_kategori' => 'Botol Kaca',
            'harga_per_kg' => 1500.00,
            'satuan' => 'kg',
            'is_active' => true,
        ]);

        $payload = [
            'nasabah_id' => $nasabah->id,
            'catatan' => 'Setoran perdana awal bulan',
            'items' => [
                [
                    'kategori_sampah_id' => $kardus->id,
                    'berat' => 10.00, // 10 * 2500 = 25000
                ],
                [
                    'kategori_sampah_id' => $botol->id,
                    'berat' => 4.00, // 4 * 1500 = 6000
                ],
            ],
        ];

        $response = $this->postJson('/api/kader/setor', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.total_berat', 14)
            ->assertJsonPath('data.total_nominal', 31000)
            ->assertJsonPath('data.saldo.sebelum', 10000)
            ->assertJsonPath('data.saldo.bertambah', 31000)
            ->assertJsonPath('data.saldo.aktif_sekarang', 41000);

        // Verifikasi tabel transaksi_setor
        $this->assertDatabaseHas('transaksi_setor', [
            'nasabah_id' => $nasabah->id,
            'kader_id' => $kader->id,
            'total_berat' => 14.00,
            'total_nominal' => 31000.00,
        ]);

        $transaksi = TransaksiSetor::where('nasabah_id', $nasabah->id)->first();
        $this->assertCount(2, $transaksi->details);

        // Verifikasi tabel mutasi_saldo
        $this->assertDatabaseHas('mutasi_saldo', [
            'nasabah_id' => $nasabah->id,
            'jenis_mutasi' => 'masuk',
            'nominal' => 31000.00,
            'saldo_sebelum' => 10000.00,
            'saldo_sesudah' => 41000.00,
            'referensi_id' => $transaksi->id,
            'tipe_referensi' => 'transaksi_setor',
        ]);

        // Verifikasi saldo nasabah terupdate
        $nasabah->refresh();
        $this->assertEquals(41000.00, $nasabah->saldo_aktif);
    }

    public function test_nasabah_cannot_record_deposit(): void
    {
        $nasabahUser = User::factory()->nasabah()->create();
        Sanctum::actingAs($nasabahUser);

        $response = $this->postJson('/api/kader/setor', [
            'nasabah_id' => 1,
            'items' => [
                ['kategori_sampah_id' => 1, 'berat' => 5],
            ],
        ]);

        $response->assertStatus(403);
    }

    public function test_validation_requires_positive_weight_and_valid_category(): void
    {
        $kader = User::factory()->kader()->create();
        Sanctum::actingAs($kader);

        $nasabah = Nasabah::factory()->create();

        $response = $this->postJson('/api/kader/setor', [
            'nasabah_id' => $nasabah->id,
            'items' => [
                ['kategori_sampah_id' => 9999, 'berat' => -2],
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['items.0.kategori_sampah_id', 'items.0.berat']);
    }
}
