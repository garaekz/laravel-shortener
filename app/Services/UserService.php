<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\AbstractUser as SocialUser;

class UserService
{
    private $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function createOrUpdateBySocialUser(SocialUser $socialUser): User {
        return $this->model->firstOrCreate(
            ['email' => $socialUser->getEmail()],
            [
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail(),
                'password' => Hash::make(Str::random(24)),
            ]
        );
    }
}
