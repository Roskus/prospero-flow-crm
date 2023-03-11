<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Email;

use Tests\TestCase;

class EmailCreateControllerTest extends TestCase
{
    /** @test */
    public function it_can_create_email(): void
    {
        $response = $this->get('/email/create');

        $response->assertOk();
        $response->assertViewIs('email.email');
        $response->assertSee('Subject');
        $response->assertSee('From');
        $response->assertSee('Attachments');
    }
}
