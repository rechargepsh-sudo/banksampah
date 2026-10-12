<?php

namespace Database\Factories;

use App\Models\MutasiSaldo;
use App\Models\Nasabah;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MutasiSaldo>
 */
class MutasiSaldoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nasabah_id' => Nasabah::factory(),
            'jenis_mutasi' => 'masuk',
            'nominal' => 25000.00,
            'saldo_sebelum' => 0.00,
            'saldo_sesudah' => 25000.00,
            'referensi_id' => null,
            'tipe_referensi' => 'transaksi_setor',
            'deskripsi' => 'Setoran sampah',
        ];
    }

    public function keluar(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis_mutasi' => 'keluar',
            'tipe_referensi' => 'transaksi_tarik_dana',
            'deskripsi' => 'Penarikan saldo',
        ]);
    }
}
