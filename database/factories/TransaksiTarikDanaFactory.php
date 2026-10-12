<?php

namespace Database\Factories;

use App\Models\Nasabah;
use App\Models\TransaksiTarikDana;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransaksiTarikDana>
 */
class TransaksiTarikDanaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode_penarikan' => 'TRK-'.now()->format('Ymd').'-'.fake()->unique()->numerify('#####'),
            'nasabah_id' => Nasabah::factory(),
            'jumlah_penarikan' => 20000.00,
            'metode_penarikan' => fake()->randomElement(['tunai', 'transfer']),
            'status' => 'pending',
            'diproses_oleh' => null,
            'tanggal_tarik' => now(),
            'catatan' => fake()->optional()->sentence(),
        ];
    }

    public function selesai(?User $kader = null): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'selesai',
            'diproses_oleh' => $kader?->id ?? User::factory()->kader(),
        ]);
    }

    public function ditolak(?User $kader = null): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'ditolak',
            'diproses_oleh' => $kader?->id ?? User::factory()->kader(),
        ]);
    }
}
