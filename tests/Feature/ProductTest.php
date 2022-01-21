<?php

namespace Tests\Feature;

use App\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateProductsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function test_a_user_can_create_a_product()
    {
        $header = $this->signIn();

        $credentials = [
            'name' => 'Sony vio',
            'brand' => 'Sony',
            'image' => 'http//unsplash.com/helloworld',
            'price' => 2000,
            'description' => 'A phone you will love'
        ];

        $this->json('POST', '/api/products', $credentials, $header)
            ->assertStatus(201);
        $this->assertDatabaseHas('products', $credentials);
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
        $this->publishProduct(['name' => ''])
            ->assertSessionHasErrors('name');
    }

    /** @test */
    function test_a_product_requires_a_brand()
    {
        $this->publishProduct(['brand' => ''])
            ->assertSessionHasErrors('brand');
    }

    /** @test */
    function test_a_product_requires_a_image()
    {
        $this->publishProduct(['image' => ''])
            ->assertSessionHasErrors('image');
    }

    /** @test */
    function test_a_product_requires_a_price()
    {
        $this->publishProduct(['price' => ''])
            ->assertSessionHasErrors('price');
    }

    /** @test */
    function test_a_product_requires_a_description()
    {
        $this->publishProduct(['description' => ''])
            ->assertSessionHasErrors('description');
    }

    /** @test */
    function test_a_product_cannot_be_updated_by_a_guest()
    {
        $this->withExceptionHandling();

        $header = $this->signIn();

        $product = create('App\Product',
            ['user_id' => create('App\User')->id]
        );

        $this->patch($product->path(), [], $header)
            ->assertStatus(403);
    }

    /** @test */
    function test_a_product_can_only_be_updated_by_its_owner()
    {
        $this->withExceptionHandling();
        $user = create('App\User');

        $token = $user->createAccessToken();
        $header = ['Authorization' => "Bearer $token"];

        $product = create('App\Product',
            ['user_id' => $user->id]
        );

        $credentials = [
            'name' => 'Sony vio',
            'brand' => 'Sony',
            'image' => 'http//unsplash.com/helloworld',
            'price' => 2000,
            'description' => 'A phone you will love'
        ];

        $this->patch($product->path(), $credentials, $header);

        tap($product->fresh(), function ($product) {
            $this->assertEquals('Sony vio', $product->name);
            $this->assertEquals('Sony', $product->brand);
            $this->assertEquals('http//unsplash.com/helloworld', $product->image);
            $this->assertEquals('2000', $product->price);
            $this->assertEquals('A phone you will love', $product->description);
        });
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

    /** @test */
    function a_product_has_a_path()
    {
        $product = create('App\Product');

        $this->assertEquals("/api/product/{$product->id}", $product->path());
    }

    /** @test */
    function a_product_has_an_owner()
    {
        $product = create('App\Product');

        $this->assertInstanceOf('App\User', $product->user);
    }

    protected function publishProduct($overrides = [])
    {
        $header = $this->withExceptionHandling()->signIn();

        $products = make('App\Product', $overrides);

        return $this->post('/api/products', $products->toArray(), $header);
    }

    protected function createAccessToken ($user) {
        return $user->createToken('App Access Token')->accessToken;
    }
}
