<?php

namespace App\Repositories;

use App\Contracts\UserRepositoryInterface;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    private $model;
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    public function findOrCreate(array $find, array $data): User
    {
        return $this->model->firstOrCreate($find, $data);
    }

    public function createSocialProvider(User $user, string $id, string $provider): User
    {
        return $user->socialProviders()->firstOrCreate(
            ['provider_id' => $id],
            ['provider' => $provider]
        );
    }
}
