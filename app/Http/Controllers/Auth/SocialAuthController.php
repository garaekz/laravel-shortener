<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialProvider;
use App\Models\User;
use App\Services\SocialAuthService;
use App\Services\SocialProviderService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    private $socialAuthService;
    private $socialProviderService;
    private $userService;

    public function __construct(
        SocialAuthService $socialAuthService,
        )
    {
        $this->socialAuthService = $socialAuthService;
    }

    public function redirect($provider)
    {
        return response()->json([
            'url' => Socialite::driver($provider)->stateless()->redirect()->getTargetUrl(),
        ]);
    }

    public function callback($provider)
    {
        try {
            $token = $this->socialAuthService->authenticateWithProvider($provider);
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json(['message' => 'Something went wrong'], 500);
        }

    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->where('name', 'authToken')->delete();
        return response()->json(['message' => 'Logged out']);
    }
}
