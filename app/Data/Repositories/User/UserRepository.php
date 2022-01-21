<?php


namespace App\Data\Repositories\User;

use App\User;
use Illuminate\Support\Facades\Auth;

class UserRepository implements UserRepositoryInterface
{
    protected $user;

    public function __construct(User $user)
    {
       $this->user = $user;
    }

    public function createUser($credentials)
    {

        $user = new User($credentials);

        $user->save();

        return $user;
    }
}
