<?php

namespace Database\Factories;

use App\Models\Nasabah;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Nasabah>
 */
class NasabahFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->nasabah(),
            'nik' => fake()->unique()->numerify('320101##########'),
            'nama' => fake()->name(),
            'no_hp' => fake()->numerify('0812########'),
            'nomor_rw' => sprintf('%02d', fake()->numberBetween(1, 10)),
            'nomor_rt' => sprintf('%02d', fake()->numberBetween(1, 15)),
            'alamat' => fake()->address(),
            'saldo_aktif' => 0.00,
            'created_by' => User::factory()->kader(),
        ];
    }
}
