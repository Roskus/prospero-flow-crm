<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Email;

use App\Mail\GenericEmail;
use App\Models\Email;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailSendControllerTest extends TestCase
{
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_can_send_email(): void
    {
        Mail::fake();

        $email = Email::factory()->create();

        $response = $this->get('/email/send/'.$email->id);

        $response->assertRedirect('/email');
        Mail::assertSent(GenericEmail::class);
        $this->equalTo($email->status, Email::SENT);
    }
}
