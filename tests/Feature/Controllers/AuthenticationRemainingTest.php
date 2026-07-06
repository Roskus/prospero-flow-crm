<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthenticationRemainingTest extends TestCase
{
    #[Test]
    public function it_blocks_unauthenticated_access_to_profile_save(): void
    {
        auth()->guard('web')->logout();

        $response = $this->post('/profile/save', [
            'first_name' => 'Test',
            'email' => 'test@example.com',
        ]);

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function it_blocks_unauthenticated_access_to_report_email(): void
    {
        auth()->guard('web')->logout();

        $response = $this->get('/report/email');

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function it_allows_authenticated_access_to_report_email(): void
    {
        $response = $this->get('/report/email');

        $response->assertOk();
    }
}
