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

        //User's data
        $data = [
            'firstname' => 'faby',
            'lastname' => 'dobo',
            'email' => 'test@example.com',
            'phonenumber' => '0816169723',
            'password' => bcrypt($password = 'example123'),
        ];

       //Send post request to /register
        $this->json('POST',route('register'),$data)
        //Assert it was successful
        ->assertStatus(201)
        ->assertJson([
            'user' => true,
            'token' => true,
            'success' => true,
            'message' => 'User Created Successfully!',
        ]);

        //Delete data
        User::where('email','test@example.com')->delete();
    }
    /** @test */
    function test_a_user_can_fetch_their_profile(){

        //User's data
        $data = [
            'firstname' => 'faby',
            'lastname' => 'dobo',
            'email' => 'test@example.com',
            'phonenumber' => '0816169723',
            'password' => bcrypt($password = 'example123'),
        ];

       //Send get request to /user
        $this->json('GET',route('userdetails'),$data)
        //Assert it was successful
        ->assertStatus(200);

        //Delete data
        User::where('email','test@example.com')->delete();
    }

    /** @test */
    function test_user_can_login_with_correct_credentials()
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

        //Delete the user
        User::where('email','test@gmail.com')->delete();
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

        $token = $user->createAcessToken();

        $header = ['Authorization' => "Bearer $token"];

        $this->json('get', '/api/products', [], $header)->assertStatus(200);
        $this->json('post', '/api/logout', [], $header)->assertStatus(200);

        $user = User::find($user->id);

        $this->assertEquals(null, $user->api_token);
    }

    public function makeUser($overrides = [])
    {
        $this->withExceptionHandling();

        $attributes = make('App\User', $overrides);

        return $this->post(route('register'), $attributes->toArray());
    }
}
