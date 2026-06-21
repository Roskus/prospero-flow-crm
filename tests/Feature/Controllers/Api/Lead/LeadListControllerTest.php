<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Lead;

use App\Models\Lead;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LeadListControllerTest extends TestCase
{
    #[Test]
    public function it_can_list_lead(): void
    {
        $this->actingAs($this->user, 'api');
        $leads = Lead::factory()->count(2)->create(['company_id' => auth()->user()->company_id, 'seller_id' => auth()->id()]);

        $response = $this->get('/api/lead');

        $response->assertOk();
        $this->assertEquals($response->json()['count'], $leads->count());
        $this->assertCount(2, $response->json()['leads']);
    }
}
