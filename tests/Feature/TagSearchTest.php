<?php

namespace Tests\Feature;

use App\Models\Tag;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class TagSearchTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_search_returns_json_suggestions_for_the_typed_letters(): void
    {
        Tag::create(['name' => 'fruits']);
        Tag::create(['name' => 'frozen food']);
        Tag::create(['name' => 'bakery']);

        $response = $this->getJson(route('tags.search', ['query' => 'fr']));

        $response->assertOk()
            ->assertJson(['suggestions' => ['frozen food', 'fruits']]);
    }

    public function test_an_empty_query_suggests_all_tags(): void
    {
        Tag::create(['name' => 'fruits']);
        Tag::create(['name' => 'bakery']);

        $response = $this->getJson(route('tags.search'));

        $response->assertOk()
            ->assertJson(['suggestions' => ['bakery', 'fruits']]);
    }

    public function test_at_most_eight_suggestions_are_returned(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            Tag::create(['name' => 'tag '.$i]);
        }

        $response = $this->getJson(route('tags.search', ['query' => 'tag']));

        $this->assertCount(8, $response->json('suggestions'));
    }

    public function test_the_search_endpoint_needs_no_product(): void
    {
        $response = $this->getJson(route('tags.search', ['query' => 'nothing-matches']));

        $response->assertOk()->assertJson(['suggestions' => []]);
    }
}
