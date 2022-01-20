<?php


namespace App\Data\Repositories\User;

use App\User;
use Illuminate\Http\Request;

class UserRepository implements UserRepositoryInterface
{
    public function createUser()
    {
        request()->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phonenumber' => 'required|string',
            'password' => 'required|string|min:6|',
        ]);

        $user = new User([
            'firstname' => request()->firstname,
            'lastname' => request()->lastname,
            'email' => request()->email,
            'phonenumber' => request()->phonenumber,
            'password' => bcrypt(request()->password)
        ]);

        $user->save();

        return $user;
    }

    public function login()
    {
        request()->validate([
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|',
        ]);

        $credentials = [
            'email' => request()->email,
            'password' => request()->password
        ];

        return $credentials;
    }
}
