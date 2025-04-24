<?php

namespace Database\Factories;

use App\Models\Rombongan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rombongan>
 */
class RombonganFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => $this->faker->country(),
            'waktu_datang' => $this->faker->dateTime()->format('H:i'),
            'waktu_pulang' => $this->faker->dateTime()->format('H:i'),
            'kode' => $this->faker->unique()->numberBetween(1, 10),
            'status' => $this->faker->randomElement(['datang', 'transaksi', 'selesai']),
            'total_belanja' => $this->faker->numberBetween(10000, 1000000),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
