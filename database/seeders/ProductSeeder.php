<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * The minimum number of products required for the test data set.
     */
    private const MINIMUM_COUNT = 20;

    /**
     * Seed the database with a catalogue that covers every status,
     * stock level and expiry situation the product views support.
     */
    public function run(): void
    {
        // The bulk of the catalogue sells normally.
        Product::factory()
            ->count($this->count() - 8)
            ->create();

        // Edge cases so every status badge and date state is visible in the UI.
        Product::factory()->inactive()->count(4)->create();
        Product::factory()->discontinued()->count(2)->create();
        Product::factory()->outOfStock()->count(2)->create();
        Product::factory()->nearlyExpired()->count(3)->create();
        Product::factory()->expired()->count(3)->create();
    }

    /**
     * Number of products to seed, always above the required minimum.
     */
    private function count(): int
    {
        return max(static::MINIMUM_COUNT, fake()->numberBetween(24, 32));
    }
}
