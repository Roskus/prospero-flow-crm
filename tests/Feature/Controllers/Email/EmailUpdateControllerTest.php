<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Email;

use App\Models\Email;
use App\Models\User;
use Tests\TestCase;

class EmailUpdateControllerTest extends TestCase
{
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_can_update_email(): void
    {
        $email = Email::factory()->create();

        $response = $this->get('/email/update/'.$email->id);

        $response->assertOk();
        $response->assertViewIs('email.email');
        $response->assertSee('Subject');
        $response->assertSee('From');
        $response->assertSee('Attachments');
        $response->assertSee($email->to);
        $response->assertSee($email->subject);
        $response->assertSee($email->body);
    }
}
