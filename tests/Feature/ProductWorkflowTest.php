<?php

namespace Tests\Feature;

use App\Enums\ProductStatus;
use App\Models\Product;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

/**
 * Integration tests: they walk through the application like a user would,
 * exercising several units (routes, controller, model, views) together.
 */
class ProductWorkflowTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_a_user_creates_a_product_and_sees_it_in_the_list_and_on_its_page(): void
    {
        $this->get(route('products.create'))
            ->assertOk();

        $response = $this->post(route('products.store'), [
            'name' => 'Sunflower Oil 750 ml',
            'description' => 'Cold-pressed for salads.',
            'expiration_date' => today()->addYear()->toDateString(),
            'status' => ProductStatus::Active->value,
            'quantity' => 12,
        ]);

        $product = Product::query()->where('name', 'Sunflower Oil 750 ml')->sole();

        $this->assertSame(12, $product->quantity);
        $this->assertSame(ProductStatus::Active, $product->status);

        $response->assertRedirect(route('products.show', $product));

        $this->get(route('products.index'))
            ->assertOk()
            ->assertSee('Sunflower Oil 750 ml');

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertSee('Sunflower Oil 750 ml')
            ->assertSee('12 pcs');
    }

    public function test_a_user_edits_a_product_and_the_changes_are_visible_on_its_page(): void
    {
        $product = Product::factory()->create(['name' => 'Greek Yogurt 400 g']);

        $this->get(route('products.edit', $product))
            ->assertOk()
            ->assertSee('Greek Yogurt 400 g');

        $this->put(route('products.update', $product), [
            'name' => 'Greek Yogurt 500 g',
            'description' => 'Extra large tub.',
            'expiration_date' => today()->addWeek()->toDateString(),
            'status' => ProductStatus::Inactive->value,
            'quantity' => 7,
        ])->assertRedirect(route('products.show', $product));

        $this->get(route('products.show', $product->fresh()))
            ->assertOk()
            ->assertSee('Greek Yogurt 500 g')
            ->assertSee('Extra large tub.')
            ->assertSee(ProductStatus::Inactive->label())
            ->assertSee('7 pcs');
    }

    public function test_a_user_cannot_store_a_product_with_invalid_data(): void
    {
        $this->post(route('products.store'), [
            'name' => '',
            'quantity' => -3,
        ])->assertRedirect()
            ->assertSessionHasErrors(['name', 'quantity', 'status']);

        $this->assertDatabaseCount('products', 0);
    }

    public function test_a_user_receives_a_delivery_then_sells_part_of_the_stock(): void
    {
        $product = Product::factory()->outOfStock()->create();

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertSee((string) route('products.change-quantity', $product), false);

        // A delivery arrives: stock grows and the new amount is shown.
        $this->post(route('products.change-quantity', $product), [
            'action' => 'increase',
            'amount' => 25,
        ])->assertRedirect(route('products.show', $product));

        $this->assertSame(25, $product->fresh()->quantity);

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertSee('25 pcs');

        // A customer buys 10 pieces: stock drops accordingly.
        $this->post(route('products.change-quantity', $product), [
            'action' => 'decrease',
            'amount' => 10,
        ])->assertRedirect(route('products.show', $product));

        $this->assertSame(15, $product->fresh()->quantity);

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertSee('15 pcs');
    }

    public function test_a_user_cannot_sell_more_than_the_store_has(): void
    {
        $product = Product::factory()->create(['quantity' => 4]);

        $this->post(route('products.change-quantity', $product), [
            'action' => 'decrease',
            'amount' => 9,
        ])->assertRedirect(route('products.show', $product));

        $this->assertSame(4, $product->fresh()->quantity);

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertSee('Quantity cannot go below zero.')
            ->assertSee('4 pcs');
    }

    public function test_a_user_deletes_a_product_and_it_disappears_from_the_list(): void
    {
        $product = Product::factory()->create();

        $this->get(route('products.index'))->assertOk()->assertSee($product->name);

        $this->delete(route('products.destroy', $product))->assertRedirect(route('products.index'));

        $this->get(route('products.index'))->assertOk()->assertDontSee($product->name);

        $this->assertModelMissing($product);
    }
}
