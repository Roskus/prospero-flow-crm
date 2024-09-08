<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Lead;

use App\Models\Lead;
use Tests\TestCase;

class LeadSaveControllerTest extends TestCase
{
    #[Test]
    public function it_can_save_lead(): void
    {
        $data = Lead::factory()->create()->toArray();
        $data['tags'] = 'one, two';

        $response = $this->post('lead/save', $data);

        $response->assertRedirect('/lead');
        $this->equalTo(Lead::all()->last(), $data);
    }

    #[Test]
    public function it_can_update_lead(): void
    {
        $data = Lead::factory()->create()->toArray();
        $data['tags'] = 'one, two';
        unset($data['id']);

        $response = $this->post('lead/save', $data);

        $response->assertRedirect('/lead');
        $this->equalTo(Lead::all()->last(), $data);
    }
}
