<?php

namespace Tests\Feature;

use App\Models\Product;

class ProductTest extends BaseTest
{
    public function test_list_products()
    {
        $products = Product::factory()->count(10)->create();

        $response = $this->getJson('/api/products');
        $response->assertStatus(200);
        $response->assertJsonCount(10, 'data');
    }
}
