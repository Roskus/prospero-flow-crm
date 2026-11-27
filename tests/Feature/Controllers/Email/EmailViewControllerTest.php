<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Email;

use App\Models\Email;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EmailViewControllerTest extends TestCase
{
    #[Test]
    public function it_can_show_email(): void
    {
        $email = Email::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->get('/email/view/'.$email->id);

        $response->assertOk();
        $response->assertViewIs('email.view');
        $response->assertSee($email->subject);
        $response->assertSee($email->body);
    }

    #[Test]
    public function it_hides_the_signature_when_it_is_not_included(): void
    {
        $this->user->signature_html = '<p>User signature block</p>';
        $this->user->save();
        $email = Email::factory()->create(['company_id' => $this->user->company_id, 'signature' => false]);

        $response = $this->get('/email/view/'.$email->id);

        $response->assertOk();
        $response->assertDontSee('User signature block', false);
    }

    #[Test]
    public function it_shows_the_signature_when_it_is_included(): void
    {
        $this->user->signature_html = '<p>User signature block</p>';
        $this->user->save();
        $email = Email::factory()->create(['company_id' => $this->user->company_id, 'signature' => true]);

        $response = $this->get('/email/view/'.$email->id);

        $response->assertOk();
        $response->assertSee('User signature block', false);
    }
}
