<?php

namespace Database\Factories;

use App\Enums\ProductStatus;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'expiration_date' => fake()->dateTimeBetween('+1 month', '+1 year'),
            'status' => ProductStatus::Active,
            'quantity' => fake()->numberBetween(0, 100),
        ];
    }
}
