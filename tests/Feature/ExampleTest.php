<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * The root path forwards visitors to the product list.
     */
    public function test_root_path_redirects_to_the_product_list(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('products.index'));
    }
}
