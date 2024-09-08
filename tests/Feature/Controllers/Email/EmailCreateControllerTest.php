<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Email;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EmailCreateControllerTest extends TestCase
{
    #[Test]
    public function it_can_create_email(): void
    {
        $response = $this->get('/email/create');

        $response->assertOk();
        $response->assertViewIs('email.email');
        $response->assertSee(__('Subject'));
        $response->assertSee(__('From'));
        $response->assertSee(__('Attachments'));
    }
}
