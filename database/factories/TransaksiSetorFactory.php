<?php

namespace Database\Factories;

use App\Models\Nasabah;
use App\Models\TransaksiSetor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransaksiSetor>
 */
class TransaksiSetorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode_transaksi' => 'STR-'.now()->format('Ymd').'-'.fake()->unique()->numerify('#####'),
            'nasabah_id' => Nasabah::factory(),
            'kader_id' => User::factory()->kader(),
            'total_berat' => 10.00,
            'total_nominal' => 25000.00,
            'tanggal_setor' => now(),
            'catatan' => fake()->optional()->sentence(),
        ];
    }
}
