<?php

namespace Database\Factories;

use App\Models\DetailTransaksiSetor;
use App\Models\KategoriSampah;
use App\Models\TransaksiSetor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DetailTransaksiSetor>
 */
class DetailTransaksiSetorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $berat = fake()->randomFloat(2, 1, 20);
        $hargaSatuan = fake()->randomElement([2000, 2500, 3000, 4500, 6000]);
        $subtotal = round($berat * $hargaSatuan, 2);

        return [
            'transaksi_setor_id' => TransaksiSetor::factory(),
            'kategori_sampah_id' => KategoriSampah::factory(),
            'berat' => $berat,
            'harga_satuan' => $hargaSatuan,
            'subtotal' => $subtotal,
        ];
    }
}
