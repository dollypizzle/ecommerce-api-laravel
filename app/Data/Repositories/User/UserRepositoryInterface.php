<?php


namespace App\Data\Repositories\User;

interface UserRepositoryInterface
{
    public function create($credentials);
}
