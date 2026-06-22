<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Unsubscribe;

use App\Models\Customer;
use App\Models\Lead;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UnsubscribeSaveControllerTest extends TestCase
{
    #[Test]
    public function it_can_unsubscribe_a_lead(): void
    {
        $lead = Lead::factory()->create(['email' => 'test@example.com', 'opt_in' => 1]);

        $response = $this->post('/unsubscribe/save', [
            'email' => 'test@example.com',
        ]);

        $response->assertRedirect('/unsubscribe');
        $response->assertSessionHas('message');
        $this->assertEquals(0, $lead->fresh()->opt_in);
    }

    #[Test]
    public function it_can_unsubscribe_a_customer(): void
    {
        $customer = Customer::factory()->create(['email' => 'customer@example.com', 'opt_in' => 1]);

        $response = $this->post('/unsubscribe/save', [
            'email' => 'customer@example.com',
        ]);

        $response->assertRedirect('/unsubscribe');
        $this->assertEquals(0, $customer->fresh()->opt_in);
    }

    #[Test]
    public function it_rejects_invalid_email(): void
    {
        $response = $this->post('/unsubscribe/save', [
            'email' => 'not-an-email',
        ]);

        $response->assertSessionHasErrors('email');
    }

    #[Test]
    public function it_rejects_missing_email(): void
    {
        $response = $this->post('/unsubscribe/save', []);

        $response->assertSessionHasErrors('email');
    }

    #[Test]
    public function it_ignores_honeypot_field(): void
    {
        $lead = Lead::factory()->create(['email' => 'test@example.com', 'opt_in' => 1]);

        $response = $this->post('/unsubscribe/save', [
            'email' => 'test@example.com',
            'website' => 'bot value',
        ]);

        $response->assertRedirect('/unsubscribe');
        $response->assertSessionMissing('message');
        $this->assertEquals(1, $lead->fresh()->opt_in);
    }
}
