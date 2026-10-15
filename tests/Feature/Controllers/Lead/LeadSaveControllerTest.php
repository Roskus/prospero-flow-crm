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

    #[Test]
    public function it_saves_country_code_in_lowercase(): void
    {
        $lead = Lead::factory()->create([
            'seller_id' => $this->user->id,
            'company_id' => $this->user->company_id,
            'country_id' => 'es',
        ]);
        $data = $lead->toArray();
        $data['tags'] = 'one, two';
        $data['country_id'] = 'FR';

        $response = $this->post('lead/save', $data);

        $response->assertRedirect('/lead');
        $this->assertSame('fr', $lead->fresh()->country_id);
    }

    #[Test]
    public function it_saves_country_by_name(): void
    {
        $lead = Lead::factory()->create([
            'seller_id' => $this->user->id,
            'company_id' => $this->user->company_id,
            'country_id' => 'es',
        ]);
        $data = $lead->toArray();
        $data['tags'] = 'one, two';
        $data['country_id'] = 'france ';

        $response = $this->post('lead/save', $data);

        $response->assertRedirect('/lead');
        $this->assertSame('fr', $lead->fresh()->country_id);
    }

    #[Test]
    public function it_rejects_unknown_country(): void
    {
        $lead = Lead::factory()->create([
            'seller_id' => $this->user->id,
            'company_id' => $this->user->company_id,
            'country_id' => 'es',
        ]);
        $data = $lead->toArray();
        $data['tags'] = 'one, two';
        $data['country_id'] = 'zz';

        $response = $this->post('lead/save', $data);

        $response->assertSessionHasErrors('country_id');
        $this->assertSame('es', $lead->fresh()->country_id);
    }
}
