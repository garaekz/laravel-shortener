<?php

namespace App\Contracts;

use App\Models\User;

interface UserRepositoryInterface
{
    public function create(array $data): User;
    public function findOrCreate(array $find, array $data): User;
    public function createSocialProvider(User $user, string $id, string $provider): User;
}
