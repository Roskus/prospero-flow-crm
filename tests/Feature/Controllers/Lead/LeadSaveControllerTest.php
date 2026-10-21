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

    #[Test]
    public function it_normalizes_formatted_phone_numbers(): void
    {
        $lead = Lead::factory()->create([
            'company_id' => $this->user->company_id,
            'seller_id' => $this->user->id,
        ]);
        $data = $lead->toArray();
        $data['tags'] = 'one, two';
        $data['phone'] = '+507 214-4700';
        $data['phone2'] = '(507) 214.4701';
        $data['mobile'] = '+34 600 123 456';

        $response = $this->post('lead/save', $data);

        $response->assertSessionHasNoErrors();
        $lead = $lead->fresh();
        $this->assertSame('5072144700', $lead->phone);
        $this->assertSame('5072144701', $lead->phone2);
        $this->assertSame('34600123456', $lead->mobile);
    }

    #[Test]
    public function it_rejects_phone_numbers_longer_than_fifteen_digits(): void
    {
        $lead = Lead::factory()->create([
            'company_id' => $this->user->company_id,
            'seller_id' => $this->user->id,
        ]);
        $data = $lead->toArray();
        $data['tags'] = 'one, two';
        $data['phone'] = '+1234 5678 9012 3456';

        $response = $this->post('lead/save', $data);

        $response->assertSessionHasErrors('phone');
    }

    #[Test]
    public function it_saves_the_extra_address(): void
    {
        $lead = Lead::factory()->create([
            'seller_id' => $this->user->id,
            'company_id' => $this->user->company_id,
        ]);
        $data = $lead->toArray();
        $data['tags'] = 'one, two';
        $data['address_extra'] = 'Oficina 12';

        $response = $this->post('lead/save', $data);

        $response->assertSessionHasNoErrors();
        $this->assertSame('Oficina 12', $lead->fresh()->address_extra);
    }

    #[Test]
    public function it_clears_an_address_field(): void
    {
        $lead = Lead::factory()->create([
            'seller_id' => $this->user->id,
            'company_id' => $this->user->company_id,
            'locality' => 'Palermo',
        ]);
        $data = $lead->toArray();
        $data['tags'] = 'one, two';
        $data['locality'] = '';

        $response = $this->post('lead/save', $data);

        $response->assertSessionHasNoErrors();
        $this->assertNull($lead->fresh()->locality);
    }

    #[Test]
    public function it_rejects_a_street_longer_than_80_characters(): void
    {
        $lead = Lead::factory()->create([
            'seller_id' => $this->user->id,
            'company_id' => $this->user->company_id,
        ]);
        $data = $lead->toArray();
        $data['tags'] = 'one, two';
        $data['street'] = str_repeat('a', 81);

        $response = $this->post('lead/save', $data);

        $response->assertSessionHasErrors('street');
    }
}
