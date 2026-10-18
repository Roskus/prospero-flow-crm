<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Models\User;
use App\Services\SecurityLogger;
use Illuminate\Support\Facades\Log;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

class ApiLoginRateLimitTest extends TestCase
{
    #[Test]
    public function it_blocks_login_after_five_failed_attempts_for_the_same_email_and_ip(): void
    {
        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->postJson('/api/auth/login', $this->wrongCredentials())->assertUnauthorized();
        }

        $response = $this->postJson('/api/auth/login', $this->wrongCredentials());

        $response->assertTooManyRequests();
        $response->assertHeader('Retry-After');
        $response->assertJson(['error' => 'Too many login attempts. Please try again later.']);
    }

    #[Test]
    public function it_blocks_even_the_correct_password_once_throttled(): void
    {
        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->postJson('/api/auth/login', $this->wrongCredentials());
        }

        $response = $this->postJson('/api/auth/login', ['email' => $this->user->email, 'password' => 'password']);

        $response->assertTooManyRequests();
        $response->assertJsonMissing(['token_type' => 'bearer']);
    }

    #[Test]
    public function it_counts_attempts_per_email_so_other_accounts_are_not_blocked(): void
    {
        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->postJson('/api/auth/login', $this->wrongCredentials());
        }

        $otherUser = User::factory()->create();

        $response = $this->postJson('/api/auth/login', ['email' => $otherUser->email, 'password' => 'password']);

        $response->assertOk();
    }

    #[Test]
    public function it_counts_attempts_per_ip_so_the_legitimate_owner_is_not_locked_out(): void
    {
        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.10'])
                ->postJson('/api/auth/login', $this->wrongCredentials());
        }

        $response = $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.20'])
            ->postJson('/api/auth/login', ['email' => $this->user->email, 'password' => 'password']);

        $response->assertOk();
    }

    #[Test]
    public function it_treats_email_case_variations_as_the_same_account(): void
    {
        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->postJson('/api/auth/login', ['email' => strtoupper($this->user->email), 'password' => 'wrong']);
        }

        $response = $this->postJson('/api/auth/login', $this->wrongCredentials());

        $response->assertTooManyRequests();
    }

    #[Test]
    public function it_logs_failed_login_attempts_with_the_attempted_account(): void
    {
        $this->mock(SecurityLogger::class, function (MockInterface $mock) {
            $mock->shouldReceive('log')
                ->once()
                ->with('api_login_failed', ['attempted_email' => $this->user->email]);
        });

        $this->postJson('/api/auth/login', $this->wrongCredentials())->assertUnauthorized();
    }

    #[Test]
    public function it_logs_throttled_login_attempts_with_the_attempted_account(): void
    {
        $this->mock(SecurityLogger::class, function (MockInterface $mock) {
            $mock->shouldReceive('log')->times(5)->with('api_login_failed', ['attempted_email' => $this->user->email]);
            $mock->shouldReceive('log')->once()->with('api_login_throttled', ['attempted_email' => $this->user->email]);
        });

        for ($attempt = 1; $attempt <= 6; $attempt++) {
            $this->postJson('/api/auth/login', $this->wrongCredentials());
        }
    }

    #[Test]
    public function security_log_entry_includes_attacker_ip_and_attempted_account(): void
    {
        $logger = Mockery::mock(LoggerInterface::class);
        $logger->shouldReceive('warning')->once()->with('security incident', Mockery::on(function (array $context): bool {
            return $context['event'] === 'api_login_failed'
                && $context['ip'] === '203.0.113.10'
                && $context['attempted_email'] === $this->user->email;
        }));
        Log::shouldReceive('channel')->with('security')->andReturn($logger);

        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.10'])
            ->postJson('/api/auth/login', $this->wrongCredentials())
            ->assertUnauthorized();
    }

    #[Test]
    public function authenticated_api_routes_are_rate_limited(): void
    {
        $response = $this->actingAs($this->user, 'api')->getJson('/api/user');

        $response->assertOk();
        $response->assertHeader('X-RateLimit-Limit', '60');
    }

    /**
     * @return array{email: string, password: string}
     */
    private function wrongCredentials(): array
    {
        return ['email' => $this->user->email, 'password' => 'wrong-password'];
    }
}
