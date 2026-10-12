<?php

namespace Database\Factories;

use App\Models\KategoriSampah;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KategoriSampah>
 */
class KategoriSampahFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_kategori' => fake()->unique()->words(2, true),
            'harga_per_kg' => fake()->randomElement([2000, 2500, 3000, 4500, 6000, 8000, 12000]),
            'satuan' => 'kg',
            'is_active' => true,
        ];
    }
}
