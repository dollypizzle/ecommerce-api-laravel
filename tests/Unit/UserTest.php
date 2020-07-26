<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function test_new_user_can_register(){
        // $this->withExceptionHandling();

        //User's data
        $credentials = [
            'firstname' => 'faby',
            'lastname' => 'dobo',
            'email' => 'test@example.com',
            'phonenumber' => '08161697231',
            'password' => bcrypt($password = 'example123'),
        ];

       //Send a post request to /register
        $this->json('POST',route('register'),$credentials)
        //Assert it was successful
        ->assertStatus(201)
        ->assertJson([
            'user' => true,
            'token' => true,
            'success' => true,
            'message' => 'User Created Successfully!',
        ]);
    }

    /** @test */
    function test_a_user_can_login_with_correct_credentials()
    {
        //Create user
        User::create([
            'firstname' => 'faby',
            'lastname' => 'dobo',
            'email' => 'test@example.com',
            'phonenumber' => '0816169723',
            'password' => bcrypt($password = 'example123'),
        ]);
        //attempt login
        $response = $this->json('POST',route('login'),[
            'email' => 'test@example.com',
            'password' => 'example123',
        ]);
        //Assert it was successful and a token was received
        $response->assertStatus(200);
        $this->assertArrayHasKey('token',$response->json());

        //Assert it was successfully added to database
        $this->assertDatabaseHas('users', [
            'firstname' => 'faby',
            'lastname' => 'dobo',
            'email' => 'test@example.com',
        ]);
    }

    /** @test */
    function test_a_user_can_fetch_their_profile(){

        //User's data
        $credentials = [
            'firstname' => 'faby',
            'lastname' => 'dobo',
            'email' => 'test@example.com',
            'phonenumber' => '0816169723',
            'password' => bcrypt($password = 'example123'),
        ];

       //Send get request to /user
        $this->json('GET',route('userdetails'),$credentials)
        //Assert it was successful
        ->assertStatus(200);

        //Delete data
        User::where('email','test@example.com')->delete();
    }

    /** @test */
    function test_user_registration_requires_a_firstname()
    {
        $this->makeUser(['firstname' => null])
            ->assertSessionHasErrors('firstname');
    }

    /** @test */
    function test_user_registration_requires_a_lastname()
    {
        $this->makeUser(['lastname' => null])
            ->assertSessionHasErrors('lastname');
    }

    /** @test */
    function user_registration_requires_a_phone_number()
    {
        $this->makeUser(['phonenumber' => null])
            ->assertSessionHasErrors('phonenumber');
    }

    /** @test */
    function test_user_registration_requires_an_email()
    {
        $this->makeUser(['email' => null])
            ->assertSessionHasErrors('email');
    }

    /** @test */
    function test_user_registration_requires_a_password()
    {
        $this->makeUser(['password' => null])
            ->assertSessionHasErrors('password');
    }

    /** @test */
    function test_a_user_can_logout()
    {
        $user = factory('App\User')->create();

        $header = $this->signIn();

        $this->json('get', '/api/products', [], $header)->assertStatus(200);
        $this->json('post', '/api/logout', [], $header)->assertStatus(200);

        $user = User::find($user->id);

        $this->assertEquals(null, $user->api_token);
    }

    /** @test */
    function a_user_may_have_many_products()
    {
        $user = create('App\User');

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $user->products);
    }

    public function makeUser($overrides = [])
    {
        $this->withExceptionHandling();

        $attributes = make('App\User', $overrides);

        return $this->post(route('register'), $attributes->toArray());
    }
}
