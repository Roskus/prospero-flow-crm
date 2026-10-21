<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Customer;

use App\Models\Customer;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CustomerSaveControllerTest extends TestCase
{
    #[Test]
    public function it_can_save_customer(): void
    {
        $data = [
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'phone' => '123456789',
            'country_id' => 'US',
            'seller_id' => $this->user->id,
            'tags' => 'one, two',
        ];

        $response = $this->post('/customer/save', $data);

        $response->assertRedirect('/customer');
        $this->assertDatabaseHas('customer', ['name' => 'Test Customer']);
    }

    #[Test]
    public function it_can_update_customer(): void
    {
        $customer = Customer::factory()->create([
            'company_id' => $this->user->company_id,
            'seller_id' => $this->user->id,
        ]);
        $data = $customer->toArray();
        $data['tags'] = 'one, two';
        $data['name'] = 'Updated Name';

        $response = $this->post('/customer/save', $data);

        $response->assertRedirect('/customer');
        $this->assertEquals('Updated Name', $customer->fresh()->name);
    }

    #[Test]
    public function it_saves_country_code_in_lowercase(): void
    {
        $customer = Customer::factory()->create([
            'company_id' => $this->user->company_id,
            'seller_id' => $this->user->id,
            'country_id' => 'es',
        ]);
        $data = $customer->toArray();
        $data['tags'] = 'one, two';
        $data['country_id'] = 'FR';

        $response = $this->post('/customer/save', $data);

        $response->assertRedirect('/customer');
        $this->assertSame('fr', $customer->fresh()->country_id);
    }

    #[Test]
    public function it_saves_country_by_name(): void
    {
        $customer = Customer::factory()->create([
            'company_id' => $this->user->company_id,
            'seller_id' => $this->user->id,
            'country_id' => 'es',
        ]);
        $data = $customer->toArray();
        $data['tags'] = 'one, two';
        $data['country_id'] = 'france ';

        $response = $this->post('/customer/save', $data);

        $response->assertRedirect('/customer');
        $this->assertSame('fr', $customer->fresh()->country_id);
    }

    #[Test]
    public function it_rejects_unknown_country(): void
    {
        $customer = Customer::factory()->create([
            'company_id' => $this->user->company_id,
            'seller_id' => $this->user->id,
            'country_id' => 'es',
        ]);
        $data = $customer->toArray();
        $data['tags'] = 'one, two';
        $data['country_id'] = 'zz';

        $response = $this->post('/customer/save', $data);

        $response->assertSessionHasErrors('country_id');
        $this->assertSame('es', $customer->fresh()->country_id);
    }

    #[Test]
    public function it_normalizes_formatted_phone_numbers(): void
    {
        $customer = Customer::factory()->create([
            'company_id' => $this->user->company_id,
            'seller_id' => $this->user->id,
        ]);
        $data = $customer->toArray();
        $data['tags'] = 'one, two';
        $data['phone'] = '+507 214-4700';
        $data['phone2'] = '(507) 214.4701';
        $data['mobile'] = '+34 600 123 456';

        $response = $this->post('/customer/save', $data);

        $response->assertSessionHasNoErrors();
        $customer = $customer->fresh();
        $this->assertSame('5072144700', $customer->phone);
        $this->assertSame('5072144701', $customer->phone2);
        $this->assertSame('34600123456', $customer->mobile);
    }

    #[Test]
    public function it_rejects_phone_numbers_longer_than_fifteen_digits(): void
    {
        $customer = Customer::factory()->create([
            'company_id' => $this->user->company_id,
            'seller_id' => $this->user->id,
        ]);
        $data = $customer->toArray();
        $data['tags'] = 'one, two';
        $data['phone'] = '+1234 5678 9012 3456';

        $response = $this->post('/customer/save', $data);

        $response->assertSessionHasErrors('phone');
    }

    #[Test]
    public function it_saves_the_extra_address(): void
    {
        $customer = Customer::factory()->create([
            'seller_id' => $this->user->id,
            'company_id' => $this->user->company_id,
        ]);
        $data = $customer->toArray();
        $data['tags'] = 'one, two';
        $data['address_extra'] = 'Oficina 12';

        $response = $this->post('customer/save', $data);

        $response->assertSessionHasNoErrors();
        $this->assertSame('Oficina 12', $customer->fresh()->address_extra);
    }

    #[Test]
    public function it_clears_an_address_field(): void
    {
        $customer = Customer::factory()->create([
            'seller_id' => $this->user->id,
            'company_id' => $this->user->company_id,
            'locality' => 'Palermo',
        ]);
        $data = $customer->toArray();
        $data['tags'] = 'one, two';
        $data['locality'] = '';

        $response = $this->post('customer/save', $data);

        $response->assertSessionHasNoErrors();
        $this->assertNull($customer->fresh()->locality);
    }

    #[Test]
    public function it_rejects_a_street_longer_than_80_characters(): void
    {
        $customer = Customer::factory()->create([
            'seller_id' => $this->user->id,
            'company_id' => $this->user->company_id,
        ]);
        $data = $customer->toArray();
        $data['tags'] = 'one, two';
        $data['street'] = str_repeat('a', 81);

        $response = $this->post('customer/save', $data);

        $response->assertSessionHasErrors('street');
    }
}
