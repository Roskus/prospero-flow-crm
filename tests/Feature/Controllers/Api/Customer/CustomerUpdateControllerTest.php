<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Customer;

use App\Models\Customer;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CustomerUpdateControllerTest extends TestCase
{
    #[Test]
    public function it_can_update_customer(): void
    {
        $this->actingAs($this->user, 'api');

        $customer = Customer::factory()->create(['company_id' => $this->user->company_id, 'seller_id' => $this->user->id]);

        $response = $this->putJson('/api/customer/'.$customer->id, [
            'name' => 'Updated Customer',
            'email' => 'updated@example.com',
            'phone' => '1234567890',
            'country_id' => 'US',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('customer', [
            'id' => $customer->id,
            'name' => 'Updated Customer',
            'email' => 'updated@example.com',
        ]);
    }

    #[Test]
    public function a_partial_update_only_changes_the_fields_sent(): void
    {
        $this->actingAs($this->user, 'api');

        $seller = User::factory()->create(['company_id' => $this->user->company_id]);
        $customer = Customer::factory()->create([
            'company_id' => $this->user->company_id,
            'seller_id' => $seller->id,
            'email' => 'keep@example.com',
            'vat' => 'B12345678',
            'phone' => '600111222',
            'notes' => 'Keep me',
            'country_id' => 'ar',
            'city' => 'Rosario',
            'street' => 'Córdoba 1234',
        ]);

        $this->putJson('/api/customer/'.$customer->id, ['business_name' => 'New Business', 'mobile' => '+34 611 222 333'])
            ->assertOk()
            ->assertJsonPath('customer.business_name', 'New Business');

        $this->assertDatabaseHas('customer', [
            'id' => $customer->id,
            'name' => $customer->name,
            'business_name' => 'New Business',
            'mobile' => '34611222333',
            'email' => 'keep@example.com',
            'vat' => 'B12345678',
            'phone' => '600111222',
            'notes' => 'Keep me',
            'seller_id' => $seller->id,
            'country_id' => 'ar',
            'city' => 'Rosario',
            'street' => 'Córdoba 1234',
        ]);
    }

    #[Test]
    public function it_updates_the_address_fields_sent(): void
    {
        $this->actingAs($this->user, 'api');

        $customer = Customer::factory()->create([
            'company_id' => $this->user->company_id,
            'seller_id' => $this->user->id,
            'country_id' => 'ar',
            'city' => 'Rosario',
        ]);

        $this->putJson('/api/customer/'.$customer->id, ['country_id' => 'ES', 'street' => 'Calle Mayor 1'])->assertOk();

        $this->assertDatabaseHas('customer', [
            'id' => $customer->id,
            'country_id' => 'es',
            'city' => 'Rosario',
            'street' => 'Calle Mayor 1',
        ]);
    }

    #[Test]
    public function it_rejects_an_invalid_or_empty_country_and_name(): void
    {
        $this->actingAs($this->user, 'api');

        $customer = Customer::factory()->create(['company_id' => $this->user->company_id, 'seller_id' => $this->user->id]);

        $this->putJson('/api/customer/'.$customer->id, ['country_id' => 'zz', 'name' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['country_id', 'name']);

        $this->putJson('/api/customer/'.$customer->id, ['country_id' => null])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['country_id']);
    }

    #[Test]
    public function it_returns_not_found_for_a_customer_of_another_company(): void
    {
        $this->actingAs($this->user, 'api');

        $customer = Customer::factory()->create();

        $this->putJson('/api/customer/'.$customer->id, ['name' => 'PWNED'])->assertNotFound();
        $this->assertNotEquals('PWNED', $customer->fresh()->name);
    }
}
