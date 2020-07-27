<?php

namespace App\Http\Controllers;

use App\Data\Repositories\User\UserRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PassportController extends Controller
{
    protected $user_repository_interface;

    public function __construct(UserRepositoryInterface $user_repository_interface)
    {
        $this->user_repository_interface = $user_repository_interface;
    }

    public function register()
    {
        $credentials = request()->validate([
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'phonenumber' => 'required|digits:11',
            'password' => 'required|string'
        ]);

        $user = $this->user_repository_interface->create($credentials);

        $token = $this->createAccessToken($user);

        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $token,
            'message' => 'User Created Successfully!',
        ], 201);
    }

    public function login()
    {
        $credentials = request()->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if(!Auth::attempt($credentials))
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);

        $user = Auth::user();

        $token = $this->createAccessToken($user);

        return response()->json([
            'user' => $user,
            'token' => $token,
            'success' => true,
            'message' => 'You Have Logged in Successfully!'
        ], 200);
    }

    public function userdetails()
    {
        $user = Auth::user();
        return response()
            ->json(['user' => $user], 200);
    }

    public function logout (Request $request) {
        $token = $request->user()->token();

        $token->revoke();

        $response = [
            'message' => 'You have logged out successfully!'
        ];

        return response($response, 200);
    }

    public function createAccessToken ($user) {
        return $user->createToken('App Access Token')->accessToken;
    }

}
