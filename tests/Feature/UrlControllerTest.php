<?php

namespace Tests\Feature;

use App\Models\CodeGeneratorConfiguration;
use App\Models\Url;
use App\Models\User;
use App\Services\UrlService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UrlControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that url is created
     */
    public function test_url_is_created()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['api:write']
        );

        $this->mock(UrlService::class, function ($mock) {
            $mock->shouldReceive('generateUrlCode')
                ->andReturn('fakeCode');
            $mock->shouldReceive('create')
                ->andReturn(Url::factory()->make(
                    [
                        'code' => 'fakeCode',
                        'original_url' => 'https://www.google.com',
                    ]
                ));
        });

        $response = $this->postJson('/api/v1/urls', [
            'url' => 'https://www.google.com',
        ]);

        $response->assertCreated()
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'code' => 'fakeCode',
                    'original_url' => 'https://www.google.com',
                ],
            ]);
    }

    /**
     * Test that url is not created when url is invalid
     */

    public function test_url_is_not_created_when_url_is_invalid()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['api:write']
        );

        $response = $this->postJson('/api/v1/urls', [
            'url' => 'invalid url',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'The url field must be a valid URL.',
                'errors' => [
                    'url' => [
                        'The url field must be a valid URL.',
                    ],
                ],
            ]);
    }

    /**
     * Test that url is not created when url is not provided
     */
    public function test_url_is_not_created_when_url_is_not_provided()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['api:write']
        );

        $response = $this->postJson('/api/v1/urls');

        $response->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'The url field is required.',
                'errors' => [
                    'url' => [
                        'The url field is required.',
                    ],
                ],
            ]);
    }

    /**
     * Test that url creation fails when user is not authenticated
     */
    public function test_all_url_routes_fail_when_user_is_not_authenticated()
    {
        // This should work for all routes in the same url group
        $response = $this->postJson('/api/v1/urls', [
            'url' => 'https://www.google.com',
        ]);

        $response->assertUnauthorized();
        $response->assertJsonStructure([
            'status',
            'message',
        ]);
    }

    /**
     * Test get single url
     */
    public function test_show_url_by_code()
    {
        $user = User::factory()->create();
        $user->urls()->save(Url::factory()->make(
            [
                'code' => 'fake',
                'original_url' => 'https://www.google.com',
            ]
        ));

        Sanctum::actingAs(
            $user,
            ['api:read']
        );

        $response = $this->getJson('/api/v1/urls/fake');

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'code' => 'fake',
                    'original_url' => 'https://www.google.com',
                ],
            ]);
    }

    /**
     * Test that url creation fails when generate code service throws
     * an exception because it couldn't generate a unique code
     */
    // public function test_url_creation_fails_when_generate_code_service_throws_an_exception() {
    //     $this->mock(UrlService::class, function ($mock) {
    //         $mock->shouldReceive('generateUrlCode')
    //             ->andThrow(new Exception('Something went wrongs....'));
    //     });

    //     Sanctum::actingAs(
    //         User::factory()->create(),
    //         ['api:write']
    //     );

    //     $response = $this->postJson('/api/v1/urls', [
    //         'url' => 'https://www.google.com',
    //     ]);

    //     $response->assertStatus(500)
    //         ->assertJson([
    //             'status' => 'error',
    //             'message' => 'Something went wrong.',
    //         ]);
    // }
}
