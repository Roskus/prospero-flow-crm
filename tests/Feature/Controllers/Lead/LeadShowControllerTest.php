<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Lead;

use App\Models\Lead;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LeadShowControllerTest extends TestCase
{
    #[Test]
    public function it_blocks_unauthenticated_access(): void
    {
        auth()->guard('web')->logout();

        $response = $this->get('/lead/show/1');

        $response->assertRedirect('/login');
    }

    #[Test]
    public function it_can_show_lead(): void
    {
        $lead = Lead::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->get('/lead/show/'.$lead->id);

        $response->assertOk();
        $response->assertViewIs('lead_customer.show');
        $response->assertSee($lead->name);
        $response->assertSee($lead->business_name);
        $response->assertSee($lead->phone);
    }

    #[Test]
    public function it_blocks_showing_another_companys_lead(): void
    {
        $lead = Lead::factory()->create(['company_id' => $this->user->company_id + 1]);

        $response = $this->get('/lead/show/'.$lead->id);

        $response->assertNotFound();
    }
}
