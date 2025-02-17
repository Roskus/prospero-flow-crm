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
        $email = Email::factory()->create();
        $email2 = Email::factory()->create();

        $response = $this->get('/email?search='.$email->subject);
        $response->assertStatus(200);
        $response->assertSee($email->subject);
        $response->assertSee($email->to);
        $response->assertDontSee($email2->subject);
        $response->assertDontSee($email2->to);
    }
}
