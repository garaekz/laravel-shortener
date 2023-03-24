<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthService {
    function authenticateWithProvider ($provider) {
        $socialUser = Socialite::driver($provider)->stateless()->user();
        $user = User::firstOrCreate(
            ['email' => $socialUser->getEmail()],
            [
                'name' => $socialUser->getName(),
                'password' => Hash::make(Str::random(24)),
            ]
        );

        $user->socialProviders()->firstOrCreate(
            ['provider_id' => $socialUser->getId()],
            ['provider' => $provider]
        );

        return $user->createToken('authToken', ['member'])->plainTextToken;
    }
}
