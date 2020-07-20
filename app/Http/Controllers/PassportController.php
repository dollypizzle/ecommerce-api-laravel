<?php

namespace App\Http\Controllers;

use App\Data\Repositories\User\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carborn\Carbon;

class PassportController extends Controller
{
    protected $user_repository_interface;

    public function __construct(UserRepositoryInterface $user_repository_interface)
    {
        $this->user_repository_interface = $user_repository_interface;
    }

    public function register()
    {
        $user = $this->user_repository_interface->createUser();

        $token = $this->createAcessToken($user);

        return response()->json([
            'user' => $user,
            'token' => $token,
            'success' => true,
            'message' => 'User Created Successfully!'
        ], 201);
    }

    public function login(){
        if(Auth::attempt([
                'email' => request('email'),
                'password' => request('password')])){
            $user = Auth::user();
            $token =  $this->createAcessToken($user);
            return response()->json([
                'user' => $user,
                'token' => $token,
                'success' => true,
                'message' => 'You Have Logged in Successfully!'
            ], 200);
        }
        else{
            return response()->json(['
                error'=>'Unauthorised User'
            ], 401);
        }
    }

    public function userdetails()
    {

        $user = Auth::user();
        return response()->json(['user' => $user], 200);
    }

    public function logout (Request $request) {
        $token = $request->user()->token();

        $token->revoke();

        $response = [
            'message' => 'You have logged out successfully!'
        ];

        return response($response, 200);
    }

    public function createAcessToken ($user) {
        return $user->createToken('App Access Token')->accessToken;
    }

}
