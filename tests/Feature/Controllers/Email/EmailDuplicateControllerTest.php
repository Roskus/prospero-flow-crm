<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Email;

use App\Models\Email;
use App\Models\User;
use Tests\TestCase;

class EmailDuplicateControllerTest extends TestCase
{
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_can_duplicate_email(): void
    {
        $email = Email::factory()->create();
        $data = [
            'email_id' => $email->id,
            'email_to' => fake()->email(),
        ];

        $response = $this->post('/email/duplicate', $data);

        $email_duplicate = Email::all()->last();

        $response->assertRedirect('email/update/'.$email_duplicate->id);
        $this->assertNotEquals($email->to, $email_duplicate->to);
        $this->assertNotEquals($email->status, Email::DRAFT);
    }
}
