<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' =>$this->faker->numberBetween(1, 3),
            'rombongan_id' =>$this->faker->numberBetween(1, 10),
            'belanja' => $this->faker->numberBetween(10000, 1000000)
        ];
    }
}
