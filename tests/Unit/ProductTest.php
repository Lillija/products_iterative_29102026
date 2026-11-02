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

    /**
     * Zero-level changes pass validation-less arithmetic: the quantity
     * must stay exactly as it was and still be persisted safely.
     */
    public function test_a_zero_difference_leaves_the_quantity_untouched(): void
    {
        $product = Product::factory()->create(['quantity' => 9]);

        $product->changeQuantity(0);

        $this->assertSame(9, $product->refresh()->quantity);
    }

    /**
     * Both intent methods are chainable (fluent): they must hand the
     * product back so callers can keep working with it.
     */
    public function test_quantity_methods_return_the_product_for_chaining(): void
    {
        $product = Product::factory()->create(['quantity' => 4]);

        $this->assertSame($product, $product->increaseQuantity(2));
        $this->assertSame($product, $product->decreaseQuantity(1));
    }

    /**
     * Real usage chains several operations: every call builds on the
     * result of the previous one (delivery, purchases, delivery, ...).
     */
    public function test_consecutive_changes_accumulate(): void
    {
        $product = Product::factory()->outOfStock()->create();

        $product->increaseQuantity(10)
            ->decreaseQuantity(4)
            ->decreaseQuantity(1)
            ->increaseQuantity(2);

        $this->assertSame(7, $product->refresh()->quantity);
    }

    public function test_decreasing_to_exactly_zero_is_allowed(): void
    {
        $product = Product::factory()->create(['quantity' => 3]);

        $product->decreaseQuantity(3);

        $this->assertSame(0, $product->refresh()->quantity);
    }

    public function test_decrease_quantity_rejects_reducing_below_zero(): void
    {
        $product = Product::factory()->create(['quantity' => 1]);

        try {
            $product->decreaseQuantity(2);
        } catch (InvalidArgumentException) {
            $this->assertSame(1, $product->fresh()->quantity);

            return;
        }

        $this->fail('decreaseQuantity() should refuse to empty an almost empty shelf.');
    }
}
