<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Email;

use App\Models\Email;
use Tests\TestCase;

class EmailDuplicateControllerTest extends TestCase
{
    /** @test */
    public function it_can_duplicate_email(): void
    {
        $email = Email::factory()->create(['status' => Email::DRAFT]);
        $data = [
            'email_id' => $email->id,
            'email_to' => fake()->email(),
        ];

        $response = $this->post('/email/duplicate', $data);

        $email_duplicate = Email::all()->last();

        $response->assertRedirect('email/update/'.$email_duplicate->id);
        $this->assertNotEquals($email->to, $email_duplicate->to);
        $this->assertEquals($email->status, Email::DRAFT);
    }
}
