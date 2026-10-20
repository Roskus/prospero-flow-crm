<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Models\Company;
use App\Services\UserCreateService;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserCreateServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    #[Test]
    public function it_hashes_the_password_and_lets_the_user_log_in_with_the_plain_one(): void
    {
        $user = (new UserCreateService)->create($this->userData('Secret-pass-123'));

        $this->assertTrue(Hash::check('Secret-pass-123', $user->fresh()->password));
        $this->assertTrue($user->fresh()->must_change_password);
        $this->assertTrue($user->hasRole('User'));

        $this->app['auth']->forgetGuards();
        $this->post('/login', ['email' => $user->email, 'password' => 'Secret-pass-123'])
            ->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function it_sends_the_plain_password_in_the_welcome_email(): void
    {
        $user = (new UserCreateService)->create($this->userData('Secret-pass-123'), sendEmail: true);

        $messages = Mail::mailer()->getSymfonyTransport()->messages();
        $this->assertCount(1, $messages);

        $body = $messages->first()->getOriginalMessage()->getTextBody();
        $this->assertStringContainsString('Your temporary password is: Secret-pass-123', $body);
        $this->assertStringNotContainsString($user->fresh()->password, $body);
    }

    #[Test]
    public function it_does_not_send_an_email_by_default(): void
    {
        (new UserCreateService)->create($this->userData('Secret-pass-123'));

        $this->assertCount(0, Mail::mailer()->getSymfonyTransport()->messages());
    }

    /**
     * @return array<string, mixed>
     */
    private function userData(string $password): array
    {
        return [
            'company_id' => Company::factory()->create()->id,
            'first_name' => 'Jane',
            'last_name' => 'Owner',
            'email' => fake()->unique()->safeEmail(),
            'lang' => 'en',
            'password' => $password,
        ];
    }
}
