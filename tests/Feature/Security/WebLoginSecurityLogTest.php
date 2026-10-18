<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Models\User;
use App\Services\SecurityLogger;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

class WebLoginSecurityLogTest extends TestCase
{
    private User $account;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app['auth']->forgetGuards();
        $this->account = User::factory()->create(['password' => Hash::make('correct-password')]);
    }

    #[Test]
    public function it_logs_failed_web_logins_with_the_attempted_account(): void
    {
        $this->mock(SecurityLogger::class, function (MockInterface $mock) {
            $mock->shouldReceive('log')->once()->with('web_login_failed', ['attempted_email' => $this->account->email]);
        });

        $this->post('/login', ['email' => $this->account->email, 'password' => 'wrong-password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest('web');
    }

    #[Test]
    public function it_logs_throttled_web_logins_with_the_attempted_account(): void
    {
        $this->mock(SecurityLogger::class, function (MockInterface $mock) {
            $mock->shouldReceive('log')->times(5)->with('web_login_failed', ['attempted_email' => $this->account->email]);
            $mock->shouldReceive('log')->once()->with('web_login_throttled', ['attempted_email' => $this->account->email]);
        });

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->post('/login', ['email' => $this->account->email, 'password' => 'wrong-password']);
        }

        $this->post('/login', ['email' => $this->account->email, 'password' => 'wrong-password'])
            ->assertSessionHasErrors('email');
        $this->assertStringContainsString(
            trans('auth.throttle', ['seconds' => 60, 'minutes' => 1]),
            session('errors')->first('email')
        );
    }

    #[Test]
    public function it_does_not_log_successful_web_logins(): void
    {
        $this->mock(SecurityLogger::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('log');
        });

        $this->post('/login', ['email' => $this->account->email, 'password' => 'correct-password'])->assertRedirect('/');
        $this->assertAuthenticatedAs($this->account, 'web');
    }

    #[Test]
    public function it_logs_login_attempts_of_inactive_users_as_failed(): void
    {
        $this->account->forceFill(['active' => false])->save();

        $this->mock(SecurityLogger::class, function (MockInterface $mock) {
            $mock->shouldReceive('log')->once()->with('web_login_failed', ['attempted_email' => $this->account->email]);
        });

        $this->post('/login', ['email' => $this->account->email, 'password' => 'correct-password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest('web');
    }

    #[Test]
    public function security_log_entry_includes_attacker_ip_and_attempted_account(): void
    {
        $logger = Mockery::mock(LoggerInterface::class);
        $logger->shouldReceive('warning')->once()->with('security incident', Mockery::on(function (array $context): bool {
            return $context['event'] === 'web_login_failed'
                && $context['ip'] === '203.0.113.10'
                && $context['attempted_email'] === $this->account->email;
        }));
        Log::shouldReceive('channel')->with('security')->andReturn($logger);

        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.10'])
            ->post('/login', ['email' => $this->account->email, 'password' => 'wrong-password'])
            ->assertSessionHasErrors('email');
    }
}
