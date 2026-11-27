<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Email;

use App\Models\Email;
use App\Models\EmailTemplate;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EmailIndexControllerTest extends TestCase
{
    #[Test]
    public function it_can_show_emails_index()
    {
        EmailTemplate::factory()->create();
        $email = Email::factory()->create(['company_id' => $this->user->company_id]);
        $email2 = Email::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->get('/email');
        $response->assertStatus(200);
        $response->assertSee($email->subject);
        $response->assertSee($email->to);
        $response->assertSee($email2->subject);
        $response->assertSee($email2->to);
    }

    #[Test]
    public function it_can_search_emails_index()
    {
        EmailTemplate::factory()->create();
        $email = Email::factory()->create(['company_id' => $this->user->company_id]);
        $email2 = Email::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->get('/email?search='.$email->subject);
        $response->assertStatus(200);
        $response->assertSee($email->subject);
        $response->assertSee($email->to);
        $response->assertDontSee($email2->subject);
        $response->assertDontSee($email2->to);
    }

    #[Test]
    public function it_shows_when_an_email_was_opened(): void
    {
        Email::factory()->create([
            'company_id' => $this->user->company_id,
            'status' => Email::SENT,
            'opened_at' => '2026-11-27 18:30:00',
        ]);

        $response = $this->get('/email');

        $response->assertOk();
        $response->assertSee('27/11/2026 18:30');
    }

    #[Test]
    public function it_filters_opened_and_not_opened_emails(): void
    {
        $opened = Email::factory()->create([
            'company_id' => $this->user->company_id,
            'status' => Email::SENT,
            'opened_at' => now(),
        ]);
        $notOpened = Email::factory()->create([
            'company_id' => $this->user->company_id,
            'status' => Email::SENT,
            'opened_at' => null,
        ]);
        $draft = Email::factory()->create([
            'company_id' => $this->user->company_id,
            'status' => 'draft',
            'opened_at' => null,
        ]);

        $this->get('/email?opened=yes')
            ->assertOk()
            ->assertSee($opened->subject)
            ->assertDontSee($notOpened->subject)
            ->assertDontSee($draft->subject);

        $this->get('/email?opened=no')
            ->assertOk()
            ->assertSee($notOpened->subject)
            ->assertDontSee($opened->subject)
            ->assertDontSee($draft->subject);
    }
}
