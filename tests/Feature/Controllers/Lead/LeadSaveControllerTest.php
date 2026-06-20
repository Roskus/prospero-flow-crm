<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Lead;

use App\Models\Lead;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LeadSaveControllerTest extends TestCase
{
    #[Test]
    public function it_can_save_lead(): void
    {
        $data = Lead::factory()->create([
            'seller_id' => $this->user->id,
            'company_id' => $this->user->company_id,
        ])->toArray();
        $data['tags'] = 'one, two';

        $response = $this->post('lead/save', $data);

        $response->assertRedirect('/lead');
    }

    #[Test]
    public function it_can_update_lead(): void
    {
        $data = Lead::factory()->create([
            'seller_id' => $this->user->id,
            'company_id' => $this->user->company_id,
        ])->toArray();
        $data['tags'] = 'one, two';
        unset($data['id']);

        $response = $this->post('lead/save', $data);

        $response->assertRedirect('/lead');
    }
}
