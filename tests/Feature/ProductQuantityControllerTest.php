<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
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

    #[DataProvider('invalidAmounts')]
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

    public function test_ajax_delivery_answers_with_json_and_the_new_quantity(): void
    {
        $product = Product::factory()->create(['quantity' => 5]);

        $response = $this->post(
            route('products.change-quantity', $product),
            ['action' => 'increase', 'amount' => 4],
            ['X-Requested-With' => 'XMLHttpRequest', 'Accept' => 'application/json'],
        );

        $response->assertOk()
            ->assertJson([
                'status' => 'Delivery registered: +4 pcs.',
                'quantity' => 9,
            ]);

        $this->assertSame(9, $product->fresh()->quantity);
    }

    public function test_ajax_purchase_that_empties_the_shelf_answers_with_422_json(): void
    {
        $product = Product::factory()->create(['quantity' => 2]);

        $response = $this->post(
            route('products.change-quantity', $product),
            ['action' => 'decrease', 'amount' => 5],
            ['X-Requested-With' => 'XMLHttpRequest', 'Accept' => 'application/json'],
        );

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Quantity cannot go below zero.',
                'errors' => ['quantity' => ['Quantity cannot go below zero.']],
            ]);

        $this->assertSame(2, $product->fresh()->quantity);
    }

    public function test_ajax_validation_fails_are_answered_with_422_json(): void
    {
        $product = Product::factory()->create(['quantity' => 2]);

        $response = $this->post(
            route('products.change-quantity', $product),
            ['action' => 'explode', 'amount' => 1],
            ['X-Requested-With' => 'XMLHttpRequest', 'Accept' => 'application/json'],
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['action']);

        $this->assertSame(2, $product->fresh()->quantity);
    }
}
