<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\Tag;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ProductSyncTagsTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_new_tag_names_are_created_in_the_database(): void
    {
        $product = Product::factory()->create();

        $product->syncTags(['fruits', 'summer']);

        $this->assertSame(
            ['fruits', 'summer'],
            $product->tags()->orderBy('name')->pluck('name')->all(),
        );
        $this->assertDatabaseCount('tags', 2);
    }

    public function test_existing_tags_are_reused_not_duplicated(): void
    {
        $other = Product::factory()->create();
        $other->syncTags(['fruits']);
        $tag = Tag::where('name', 'fruits')->sole();

        $product = Product::factory()->create();
        $product->syncTags(['fruits']);

        $this->assertDatabaseCount('tags', 1);
        $this->assertSame($tag->id, $product->tags()->sole()->id);
        $this->assertSame(2, $tag->products()->count());
    }

    public function test_tags_not_in_the_submission_are_detached(): void
    {
        $product = Product::factory()->create();
        $product->syncTags(['fruits', 'summer']);

        $product->syncTags(['summer']);

        $this->assertSame(['summer'], $product->tags()->pluck('name')->all());
        $this->assertDatabaseCount('tags', 2);
    }

    public function test_blank_and_duplicate_names_collapse_into_one_tag(): void
    {
        $product = Product::factory()->create();

        $product->syncTags(['  fruits ', 'fruits', '', '   ']);

        $this->assertSame(['fruits'], $product->tags()->pluck('name')->all());
        $this->assertDatabaseCount('tags', 1);
    }

    public function test_an_empty_list_clears_the_tags(): void
    {
        $product = Product::factory()->create();
        $product->syncTags(['fruits', 'summer']);

        $product->syncTags([]);

        $this->assertSame(0, $product->tags()->count());
        $this->assertDatabaseCount('product_tag', 0);
    }

    public function test_sync_tags_is_chainable(): void
    {
        $product = Product::factory()->create();

        $this->assertSame($product, $product->syncTags(['fruits']));
    }
}
