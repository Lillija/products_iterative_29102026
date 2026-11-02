<?php

namespace Tests\Unit;

use App\Models\Product;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_increase_quantity_adds_stock_and_persists_it(): void
    {
        $product = Product::factory()->outOfStock()->create();

        $product->increaseQuantity(7);

        $product->refresh();

        $this->assertSame(7, $product->quantity);
    }

    public function test_decrease_quantity_removes_stock_and_persists_it(): void
    {
        $product = Product::factory()->create(['quantity' => 12]);

        $product->decreaseQuantity(4);

        $product->refresh();

        $this->assertSame(8, $product->quantity);
    }

    public function test_increase_quantity_defaults_to_one_piece(): void
    {
        $product = Product::factory()->create(['quantity' => 3]);

        $product->increaseQuantity();

        $this->assertSame(4, $product->refresh()->quantity);
    }

    public function test_change_quantity_stores_a_negative_difference_as_a_purchase(): void
    {
        $product = Product::factory()->create(['quantity' => 10]);

        $product->changeQuantity(-10);

        $this->assertSame(0, $product->refresh()->quantity);
    }

    public function test_change_quantity_rejects_reducing_below_zero(): void
    {
        $product = Product::factory()->create(['quantity' => 2]);

        try {
            $product->changeQuantity(-3);
        } catch (InvalidArgumentException) {
            $this->assertSame(2, $product->fresh()->quantity);

            return;
        }

        $this->fail('Reducing the quantity below zero should throw InvalidArgumentException.');
    }

    public function test_change_quantity_does_not_persist_when_it_is_rejected(): void
    {
        $product = Product::factory()->outOfStock()->create();

        try {
            $product->increaseQuantity(-2);
        } catch (InvalidArgumentException) {
            $this->assertSame(0, $product->fresh()->quantity);

            return;
        }

        $this->fail('A change that would drop the quantity below zero should not be persisted.');
    }
}
