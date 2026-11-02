<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\Tag;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class TagTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_a_tag_is_created_with_its_name(): void
    {
        $tag = Tag::create(['name' => 'fruits']);

        $this->assertModelExists($tag);
        $this->assertSame('fruits', $tag->fresh()->name);
    }

    public function test_tag_names_are_unique(): void
    {
        Tag::create(['name' => 'fruits']);

        $this->expectException(QueryException::class);

        Tag::create(['name' => 'fruits']);
    }

    public function test_a_tag_belongs_to_several_products(): void
    {
        $tag = Tag::create(['name' => 'fruits']);
        $first = Product::factory()->create();
        $second = Product::factory()->create();

        $first->syncTags(['fruits']);
        $second->syncTags(['fruits']);

        $this->assertSame(2, $tag->products()->count());
    }
}
