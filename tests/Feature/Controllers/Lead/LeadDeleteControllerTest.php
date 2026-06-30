<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Lead;

use App\Models\Lead;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LeadDeleteControllerTest extends TestCase
{
    #[Test]
    public function it_can_delete_lead()
    {
        $lead = Lead::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->get('/lead/delete/'.$lead->id);

        $response->assertRedirect('/lead');
        $response->assertSessionHas('message', 'Lead deleted successfully');
        $this->assertDatabaseMissing('lead', $lead->toArray());
    }
}
