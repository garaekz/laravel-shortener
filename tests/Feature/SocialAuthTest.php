<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\SocialAuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Tests\TestCase;

class SocialAuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Tests that the auth url is returned for the social provider
     */

    public function test_get_social_provider_auth_url()
    {
        Socialite::shouldReceive('driver->stateless->redirect->getTargetUrl')
            ->once()
            ->andReturn('fakeUrl');

        $response = $this->get('/api/auth/google/redirect');

        $response->assertOk()
            ->assertJson([
                'url' => 'fakeUrl',
            ]);
    }

    /**
     * Tests that the access token is returned for the social provider
     */

    public function test_callback_returns_token()
    {
        $this->mock(SocialAuthService::class, function ($mock) {
            $mock->shouldReceive('authenticateWithProvider')
                ->once()
                ->andReturn('fakeToken');
        });

        $response = $this->get('/api/auth/google/callback');

        $response->assertOk()
            ->assertJson([
                'access_token' => 'fakeToken',
                'token_type' => 'Bearer',
            ]);
    }

    /**
     * Tests when callback fails
     */
    public function test_callback_fails()
    {
        $this->mock(SocialAuthService::class, function ($mock) {
            $mock->shouldReceive('authenticateWithProvider')
                ->once()
                ->andThrow(new \Exception('fake error'));
        });

        $response = $this->get('/api/auth/google/callback');

        $response->assertServerError()
            ->assertJson([
                'message' => 'Something went wrong',
            ]);
    }

    /**
     * Tests the logout functionality
     */
    public function test_logout()
    {
        $user = User::factory()->create();
        $user->createToken('authToken')->plainTextToken;

        $response = $this->actingAs($user)
            ->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Logged out',
            ]);

        $this->assertCount(0, $user->tokens);
    }

    /**
     * Tests the logout functionality without token
     */
    public function test_logout_without_token()
    {
        $response = $this->postJson('/api/auth/logout');

        $response->assertUnauthorized()
            ->assertJson([
                'status' => 'error',
                'message' => 'Unauthenticated.',
            ]);
    }

    /**
     * Tests the user is authenticated
     */
    public function test_user_is_authenticated()
    {

        $response = $this->actingAs(User::factory()->create())
            ->getJson('/api/user');

        $response->assertOk()
            ->assertJsonStructure([
                'id',
                'name',
                'email',
                'email_verified_at',
                'created_at',
                'updated_at',
            ]);
    }

    /**
     * Tests the user is not authenticated
     */
    public function test_user_is_not_authenticated()
    {
        $response = $this->getJson('/api/user');

        $response->assertUnauthorized()
            ->assertJson([
                'status' => 'error',
                'message' => 'Unauthenticated.',
            ]);
    }
}
