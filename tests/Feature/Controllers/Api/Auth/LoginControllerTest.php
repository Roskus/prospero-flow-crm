<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Auth;

use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    private const string LOGIN_URL = '/api/auth/login';

    #[Test]
    // #[CoversMethod('App\Http\Controllers\Api\Auth\LoginController', 'login')]
    public function it_can_login()
    {
        $credentials = ['email' => $this->user->email, 'password' => 'password'];

        $response = $this->post(self::LOGIN_URL, $credentials);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'access_token', 'token_type', 'expires_in',
            ]);
        $this->isAuthenticated('api');
    }

    #[Test]
    public function it_can_not_login_with_wrong_credentials()
    {
        $credentials = ['email' => $this->user->email, 'password' => 'wrong_password'];

        $response = $this->post(self::LOGIN_URL, $credentials);

        $response
            ->assertStatus(401)
            ->assertExactJson(['error' => 'Unauthorized']);
        $this->assertGuest('api');
    }

    #[Test]
    public function it_can_get_credentials()
    {
        $this->actingAs($this->user, 'api');

        $response = $this->post('/api/auth/me');

        $response
            ->assertStatus(200)
            ->assertExactJson($this->user->makeVisible('company_id')->toArray())
            ->assertJsonPath('company_id', $this->user->company_id);
    }

    #[Test]
    public function it_can_logout()
    {
        $credentials = ['email' => $this->user->email, 'password' => 'password'];
        auth('api')->attempt($credentials);

        $response = $this->post('/api/auth/logout');

        $response
            ->assertStatus(200)
            ->assertExactJson(['message' => 'Successfully logged out']);

        $this->assertNull(auth('api')->user());
        $this->assertGuest('api');
    }

    #[Test]
    public function it_can_refresh_token()
    {
        $credentials = ['email' => $this->user->email, 'password' => 'password'];
        auth('api')->attempt($credentials);
        $user_token = auth('api')->tokenById($this->user->id);

        $response = $this->post('/api/auth/refresh');
        $refresh_user_token = auth('api')->tokenById($this->user->id);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'access_token', 'token_type', 'expires_in',
            ]);

        $this->assertNotEquals($user_token, $refresh_user_token);
    }

    #[Test]
    public function login_reports_the_default_token_lifetime_in_seconds(): void
    {
        $response = $this->post(self::LOGIN_URL, ['email' => $this->user->email, 'password' => 'password']);

        $response->assertOk()->assertJsonPath('expires_in', 3600);
    }

    #[Test]
    public function login_and_refresh_report_the_configured_token_lifetime_in_seconds(): void
    {
        auth('api')->factory()->setTTL(15);

        $login = $this->post(self::LOGIN_URL, ['email' => $this->user->email, 'password' => 'password']);
        $login->assertOk()->assertJsonPath('expires_in', 900);

        $refresh = $this->withToken($login->json('access_token'))->post('/api/auth/refresh');
        $refresh->assertOk()->assertJsonPath('expires_in', 900);
    }

    #[Test]
    public function the_token_lifetime_falls_back_to_60_minutes_when_not_configured(): void
    {
        foreach ([false, ''] as $configuredTtl) {
            $configuredTtl === false ? putenv('JWT_TTL') : putenv('JWT_TTL='.$configuredTtl);
            $config = require config_path('jwt.php');

            $this->assertSame(60, $config['ttl']);
        }

        putenv('JWT_TTL');
    }
}
