<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ProductQuantityControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_increase_route_registers_a_delivery_and_redirects_back(): void
    {
        $product = Product::factory()->create(['quantity' => 5]);

        $response = $this->post(route('products.change-quantity', $product), [
            'action' => 'increase',
            'amount' => 10,
        ]);

        $response->assertRedirect(route('products.show', $product))
            ->assertSessionHas('status', 'Delivery registered: +10 pcs.');

        $this->assertSame(15, $product->fresh()->quantity);
    }

    public function test_decrease_route_registers_a_purchase_and_redirects_back(): void
    {
        $product = Product::factory()->create(['quantity' => 20]);

        $response = $this->post(route('products.change-quantity', $product), [
            'action' => 'decrease',
            'amount' => 6,
        ]);

        $response->assertRedirect(route('products.show', $product))
            ->assertSessionHas('status', 'Purchase registered: -6 pcs.');

        $this->assertSame(14, $product->fresh()->quantity);
    }

    public function test_decrease_below_zero_is_rejected_and_stock_is_untouched(): void
    {
        $product = Product::factory()->outOfStock()->create();

        $response = $this->from(route('products.show', $product))
            ->post(route('products.change-quantity', $product), [
                'action' => 'decrease',
                'amount' => 5,
            ]);

        $response->assertRedirect(route('products.show', $product))
            ->assertSessionHasErrors(['quantity' => 'Quantity cannot go below zero.']);

        $this->assertSame(0, $product->fresh()->quantity);
    }

    public function test_unknown_action_is_rejected(): void
    {
        $product = Product::factory()->create(['quantity' => 5]);

        $response = $this->post(route('products.change-quantity', $product), [
            'action' => 'steal',
            'amount' => 1,
        ]);

        $response->assertRedirect()
            ->assertSessionHasErrors(['action']);

        $this->assertSame(5, $product->fresh()->quantity);
    }

    public static function invalidAmounts(): array
    {
        return [
            'zero amount' => ['0'],
            'negative amount' => ['-2'],
            'non-numeric amount' => ['a lot'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('invalidAmounts')]
    public function test_invalid_amount_is_rejected_and_stock_is_untouched(string $amount): void
    {
        $product = Product::factory()->create(['quantity' => 5]);

        $response = $this->post(route('products.change-quantity', $product), [
            'action' => 'increase',
            'amount' => $amount,
        ]);

        $response->assertRedirect()
            ->assertSessionHasErrors(['amount']);

        $this->assertSame(5, $product->fresh()->quantity);
    }

    public function test_amount_may_reach_exactly_zero_stock_on_purchase(): void
    {
        $product = Product::factory()->create(['quantity' => 3]);

        $response = $this->post(route('products.change-quantity', $product), [
            'action' => 'decrease',
            'amount' => 3,
        ]);

        $response->assertSessionHas('status');

        $this->assertSame(0, $product->fresh()->quantity);
    }
}
