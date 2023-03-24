<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\SocialAuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Socialite\Facades\Socialite;
use Tests\TestCase;

class SocialAuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Tests that the auth url is returned for the social provider
     */

    public function test_get_social_provider_auth_url() {
        Socialite::shouldReceive('driver->stateless->redirect->getTargetUrl')
            ->once()
            ->andReturn('fakeUrl');

        $response = $this->get('/api/v1/auth/google/redirect');

        $response->assertStatus(200);
        $response->assertJson([
            'url' => 'fakeUrl',
        ]);
    }

    /**
     * Tests that the access token is returned for the social provider
     */

    public function test_callback_returns_token() {
        // We need to fake the service
        $this->mock(SocialAuthService::class, function ($mock) {
            $mock->shouldReceive('authenticateWithProvider')
                ->once()
                ->andReturn('fakeToken');
        });

        $response = $this->get('/api/v1/auth/google/callback');

        $response->assertStatus(200);
        $response->assertJson([
            'access_token' => 'fakeToken',
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Tests when callback fails
     */

    public function test_callback_fails() {
        // We need to fake the service
        $this->mock(SocialAuthService::class, function ($mock) {
            $mock->shouldReceive('authenticateWithProvider')
                ->once()
                ->andThrow(new \Exception('fake error'));
        });

        $response = $this->get('/api/v1/auth/google/callback');

        $response->assertStatus(500);
        $response->assertJson([
            'message' => 'Something went wrong',
        ]);
    }

    /**
     * Tests the logout functionality
     */
    public function test_logout() {
        // We need to fake the request logeed in user
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/v1/auth/logout');

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Logged out',
        ]);

        // Check if the token is deleted
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => 'App\Models\User',
            'name' => 'test',
        ]);
    }
}
