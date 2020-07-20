<?php

namespace Tests\Feature;

use App\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateProductsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function test_guests_cannnot_create_products()
    {
        $this->withExceptionHandling();

        $this->post('/api/products')
            ->assertRedirect('/api/login');
    }

    /** @test */
    function test_a_user_can_create_a_product()
    {
        $header = $this->signIn();

        $data = [
            'name' => 'Sony vio',
            'brand' => 'Sony',
            'image' => 'http//unsplash.com/helloworld',
            'price' => 2000,
            'description' => 'A phone you will love'
        ];

        $this->json('POST', '/api/products', $data, $header)
            ->assertStatus(200);
    }

    /** @test */
    function test_a_guest_cannot_create_a_product()
    {
        $header = $this->withExceptionHandling()->signIn();

        $this->post('/api/products', $header)
            ->assertRedirect('/api/login')
            ->assertStatus(302);
    }

    /** @test */
    function test_a_user_can_view_all_products()
    {
        $product1 = factory('App\Product')->create([
            'name' => 'Sony vio',
            'brand' => 'Sony',
            'image' => 'http//unsplash.com/helloworld',
            'price' => 2000,
            'description' => 'A phone you will love'
        ]);

        $product2 = factory('App\Product')->create([
            'name' => 'Sony vio',
            'brand' => 'Sony',
            'image' => 'http//unsplash.com/helloworld',
            'price' => 2000,
            'description' => 'A phone you will love'
        ]);

        $response = $this->json('GET', '/api/products', [])
            ->assertStatus(200)
            ->assertSee($product1->name, $product2->name);
    }

    /** @test */
    function test_a_user_can_read_a_single_product()
    {
        $product = factory('App\Product')->create([
            'name' => 'Sony vio',
            'brand' => 'Sony',
            'image' => 'http//unsplash.com/helloworld',
            'price' => 2000,
            'description' => 'A phone you will love'
        ]);

        $this->get($product->path())
            ->assertSee($product->name)
            ->assertSee($product->brand);
    }

    /** @test */
    function test_a_product_requires_a_name()
    {
        // $this->withExceptionHandling();

        $this->publishProduct(['name' => ''])
            ->assertSessionHasErrors('name');
    }

    /** @test */
    function test_a_product_requires_a_brand()
    {
        // $this->withExceptionHandling();

        $this->publishProduct(['brand' => ''])
            ->assertSessionHasErrors('brand');
    }

    /** @test */
    function test_a_product_requires_a_image()
    {
        // $this->withExceptionHandling();

        $this->publishProduct(['image' => ''])
            ->assertSessionHasErrors('image');
    }

    /** @test */
    function test_a_product_requires_a_price()
    {
        // $this->withExceptionHandling();

        $this->publishProduct(['price' => ''])
            ->assertSessionHasErrors('price');
    }

    /** @test */
    function test_a_product_requires_a_description()
    {
        // $this->withExceptionHandling();

        $this->publishProduct(['description' => ''])
            ->assertSessionHasErrors('description');
    }

    /** @test */
    function test_a_product_can_only_be_updated_by_owner()
    {
        $header = $this->signIn();

        $product = factory(Product::class)->create([
            'name' => 'Sony vio',
            'brand' => 'Sony',
            'image' => 'http//unsplash.com/helloworld',
            'price' => 2000,
            'description' => 'A phone you will love'
        ]);

        $payload = [
            'name' => 'Sony',
        ];

        $response = $this->json('PATCH', '/api/product/' . $product->id, $payload, $header)
            ->assertStatus(200);
    }

    /** @test */
    function test_a_guest_cannot_edit_a_product()
    {
        $header = $this->withExceptionHandling()->signIn();

        $this->patch('/api/product/1', $header)
            ->assertRedirect('/api/login')
            ->assertStatus(302);
    }

    function test_a_product_can_only_be_deleted_by_owner()
    {
        $header = $this->signIn();

        $product = factory(Product::class)->create([
            'name' => 'Sony vio',
            'brand' => 'Sony',
            'image' => 'http//unsplash.com/helloworld',
            'price' => 2000,
            'description' => 'A phone you will love'
        ]);

        $this->json('DELETE', '/api/product/' . $product->id, [], $header)
            ->assertStatus(200);
    }

    protected function publishProduct($overrides = [])
    {
        $header = $this->withExceptionHandling()->signIn();

        $products = make('App\Product', $overrides);

        return $this->post('/api/products', $products->toArray(), $header);
    }
}
