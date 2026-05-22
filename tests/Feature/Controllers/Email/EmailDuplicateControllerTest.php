<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Email;

use App\Models\Email;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EmailDuplicateControllerTest extends TestCase
{
    #[Test]
    public function it_blocks_unauthenticated_access(): void
    {
        auth()->guard('web')->logout();

        $response = $this->post('/email/duplicate', []);

        $response->assertRedirect('/login');
    }

    #[Test]
    public function it_can_duplicate_email(): void
    {
        $email = Email::factory()->create([
            'company_id' => $this->user->company_id,
            'status' => Email::DRAFT,
        ]);
        $data = [
            'email_id' => $email->id,
            'to' => fake()->email(),
        ];

        $response = $this->post('/email/duplicate', $data);

        $email_duplicate = Email::all()->last();

        $response->assertRedirect('email/update/'.$email_duplicate->id);
        $this->assertNotEquals($email->to, $email_duplicate->to);
        $this->assertEquals($email->status, Email::DRAFT);
    }

    #[Test]
    public function it_blocks_duplicating_another_companys_email(): void
    {
        $email = Email::factory()->create([
            'company_id' => $this->user->company_id + 1,
            'status' => Email::DRAFT,
        ]);

        $response = $this->post('/email/duplicate', [
            'email_id' => $email->id,
            'to' => fake()->email(),
        ]);

        $response->assertNotFound();
    }
}
