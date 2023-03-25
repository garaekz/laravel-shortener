<?php

namespace App\Services;

use App\Contracts\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthService {

    private $userRepository;
    public function __construct(UserRepositoryInterface $userRepository) {
        $this->userRepository = $userRepository;
    }

    function authenticateWithProvider ($provider) {
        $socialUser = Socialite::driver($provider)->stateless()->user();
        $user = $this->userRepository->findOrCreate([
            'email' => $socialUser->getEmail(),
        ], [
            'name' => $socialUser->getName(),
            'password' => Hash::make(Str::random(24)),
        ]);

        $this->userRepository->createSocialProvider(
            $user,
            $socialUser->getId(),
            $provider
        );

        return $user->createToken('authToken', ['member'])->plainTextToken;
    }
}
