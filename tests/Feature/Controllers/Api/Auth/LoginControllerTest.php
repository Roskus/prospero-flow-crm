<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Auth;

use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    #[Test]
    // #[CoversMethod('App\Http\Controllers\Api\Auth\LoginController', 'login')]
    public function it_can_login()
    {
        $credentials = ['email' => $this->user->email, 'password' => 'password'];

        $response = $this->post('/api/auth/login', $credentials);

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

        $response = $this->post('/api/auth/login', $credentials);

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
            ->assertExactJson($this->user->toArray());
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
}
