<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Email;

use App\Models\Email;
use App\Models\User;
use Tests\TestCase;

class EmailViewControllerTest extends TestCase
{
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_can_show_email(): void
    {
        $email = Email::factory()->create();

        $response = $this->get('/email/view/'.$email->id);

        $response->assertOk();
        $response->assertViewIs('email.view');
        $response->assertSee($email->subject);
        $response->assertSee($email->body);
    }
}
