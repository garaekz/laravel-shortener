<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\SocialAuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Tests\TestCase;

class SocialAuthServiceTest extends TestCase
{
    use RefreshDatabase;

    private $service;

    // TODO: Properly refactor this test and service to be independent from external services
    public function setUp(): void
    {
        parent::setUp();

        Socialite::shouldReceive('driver->stateless->user->getId')
            ->once()
            ->andReturn('fakeId');
        Socialite::shouldReceive('driver->stateless->user->getEmail')
            ->once()
            ->andReturn('fakeEmail');
        Socialite::shouldReceive('driver->stateless->user->getName')
            ->once()
            ->andReturn('fakeName');

        $this->mock(User::class, function ($mock) {
            $mock->shouldReceive('firstOrCreate')
                ->andReturn((object) [
                    'id' => 1,
                    'name' => 'fakeName',
                    'email' => 'fakeEmail',
                    'password' => 'fakePassword',
                ]);
        });

        $this->service = new SocialAuthService();
    }

    /**
     * Test that authenticateWithProvider returns a token
     */

    public function test_authenticate_with_provider_returns_token()
    {
        $token = $this->service->authenticateWithProvider('google');
        $this->assertNotNull($token);
    }

    /**
     * Test that authenticateWithProvider creates a user
     */

    public function test_authenticate_with_provider_creates_user()
    {
        $this->mock(User::class, function ($mock) {
            $mock->shouldReceive('firstOrCreate')
                ->andReturn((object) [
                    'name' => 'fakeName',
                    'email' => 'fakeEmail',
                    'password' => 'fakePassword',
                ]);
        });

        $this->service->authenticateWithProvider('google');
        $this->assertDatabaseHas('users', [
            'id' => 1,
            'name' => 'fakeName',
            'email' => 'fakeEmail',
        ]);
    }

    /**
     * Test that authenticateWithProvider creates a social provider
     */

    public function test_authenticate_with_provider_creates_social_provider()
    {
        $this->mock(User::class, function ($mock) {
            $mock->shouldReceive('firstOrCreate')
                ->andReturn((object) [
                    'id' => 1,
                    'name' => 'fakeName',
                    'email' => 'fakeEmail',
                    'password' => 'fakePassword',
                ]);
        });

        $this->service->authenticateWithProvider('google');
        $this->assertDatabaseHas('social_providers', [
            'user_id' => 1,
            'provider_id' => 'fakeId',
            'provider' => 'google',
        ]);
    }
}
