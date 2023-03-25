<?php

namespace Tests\Unit;

use App\Contracts\UserRepositoryInterface;
use App\Models\SocialProvider;
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

        $mockUserRepository = $this->createMock(UserRepositoryInterface::class);
        $user = $this->createMock(User::class);
        // Mock plainTextToken

        $user->method('createToken')
            ->willReturn((object) ['plainTextToken' => 'fakeToken' ]);

        $mockUserRepository->method('findOrCreate')
            ->willReturn($user);
        $mockUserRepository->method('createSocialProvider')
            ->willReturn($user);

        $this->service = new SocialAuthService($mockUserRepository);
    }

    /**
     * Test that authenticateWithProvider returns a token
     */

    public function test_authenticate_with_provider_returns_token()
    {
        $token = $this->service->authenticateWithProvider('google');
        $this->assertNotNull($token);
        $this->assertEquals('fakeToken', $token);
    }
}
