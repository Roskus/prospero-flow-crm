<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Email;

use App\Mail\GenericEmail;
use App\Models\Email;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EmailSendControllerTest extends TestCase
{
    #[Test]
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
