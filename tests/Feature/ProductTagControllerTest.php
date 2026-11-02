<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Tag;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ProductTagControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_new_tags_are_created_and_attached_on_the_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->put(route('products.tags.update', $product), [
            'tags' => ['fruits', 'summer'],
        ]);

        $response->assertRedirect(route('products.show', $product))
            ->assertSessionHas('status', 'Tags updated.');

        $this->assertSame(
            ['fruits', 'summer'],
            $product->tags()->orderBy('name')->pluck('name')->all(),
        );
        $this->assertDatabaseCount('tags', 2);
    }

    public function test_previously_set_tags_are_replaced_by_the_submission(): void
    {
        $product = Product::factory()->create();
        $product->syncTags(['fruits', 'summer']);
        $summer = Tag::where('name', 'summer')->sole();

        $this->put(route('products.tags.update', $product), [
            'tags' => ['summer', 'chilled'],
        ]);

        $this->assertSame(
            ['chilled', 'summer'],
            $product->tags()->orderBy('name')->pluck('name')->all(),
        );
        // Detached names keep existing as reusable tag rows.
        $this->assertDatabaseCount('tags', 3);
    }

    public function test_removing_all_tags_from_the_editor_clears_the_pivot(): void
    {
        $product = Product::factory()->create();
        $product->syncTags(['fruits']);

        $response = $this->put(route('products.tags.update', $product));

        $response->assertRedirect(route('products.show', $product));

        $this->assertSame(0, $product->tags()->count());
    }

    public function test_a_submission_without_the_field_keeps_the_tags(): void
    {
        $product = Product::factory()->create();
        $product->syncTags(['fruits']);

        $this->put(route('products.tags.update', $product), ['tags' => ['fruits']]);

        $this->assertSame(['fruits'], $product->refresh()->tags()->pluck('name')->all());
    }

    public function test_a_tag_longer_than_the_limit_is_rejected(): void
    {
        $product = Product::factory()->create();

        $response = $this->from(route('products.show', $product))
            ->put(route('products.tags.update', $product), [
                'tags' => [str_repeat('a', 51)],
            ]);

        $response->assertRedirect(route('products.show', $product))
            ->assertSessionHasErrors();

        $this->assertDatabaseCount('tags', 0);
        $this->assertDatabaseCount('product_tag', 0);
    }

    public function test_attached_tags_are_listed_on_the_show_page(): void
    {
        $product = Product::factory()->create();
        $product->syncTags(['fruits', 'summer']);

        $response = $this->get(route('products.show', $product));

        $response->assertOk()
            ->assertSee('fruits')
            ->assertSee('summer')
            ->assertSee((string) route('products.tags.update', $product), false);
    }
}
