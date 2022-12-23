<?php

namespace Tests\Feature\Controllers\Email;

use App\Models\Email;
use App\Models\EmailTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailIndexControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_show_emails_index()
    {
        $user = $this->signIn();
        $this->actingAs($user);

        EmailTemplate::factory()->create();
        $email = Email::factory()->create();
        $email2 = Email::factory()->create();

        $response = $this->get('/email');
        $response->assertStatus(200);
        $response->assertSee($email->subject);
        $response->assertSee($email->to);
        $response->assertSee($email2->subject);
        $response->assertSee($email2->to);

        $response = $this->get('/email?search='.$email->subject);
        $response->assertStatus(200);
        $response->assertSee($email->subject);
        $response->assertSee($email->to);
        $response->assertDontSee($email2->subject);
        $response->assertDontSee($email2->to);
    }
}
