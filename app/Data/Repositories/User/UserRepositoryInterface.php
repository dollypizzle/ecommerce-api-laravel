<?php


namespace App\Data\Repositories\User;

interface UserRepositoryInterface
{
    public function createUser();

    public function login();
}
