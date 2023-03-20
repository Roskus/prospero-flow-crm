<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Email;

use App\Models\Email;
use Tests\TestCase;

class EmailUpdateControllerTest extends TestCase
{
    /** @test */
    public function it_can_update_email(): void
    {
        $email = Email::factory()->create();

        $response = $this->get('/email/update/'.$email->id);

        $response->assertOk();
        $response->assertViewIs('email.email');
        $response->assertSee(__('Subject'));
        $response->assertSee(__('From'));
        $response->assertSee(__('Attachments'));
        $response->assertSee($email->to);
        $response->assertSee($email->subject);
        $response->assertSee($email->body);
    }
}
