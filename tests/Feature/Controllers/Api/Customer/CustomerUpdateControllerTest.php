<?php

namespace Tests\Feature\Controllers\Api\Customer;

use App\Models\Customer;
use App\Models\User;
use Tests\TestCase;

class CustomerUpdateControllerTest extends TestCase
{
    /** @test */
    public function it_can_update_customer(): void
    {
        $this->actingAs(User::factory()->create(), 'api');

        $customer = Customer::factory()->create(['seller_id' => auth()->id()])->toArray();
        $customer['tags'] = implode(',', $customer['tags']);

        $updatedCustomer = array_except(Customer::factory()->create(['seller_id' => auth()->id()])->toArray(), 'id');
        $updatedCustomer['tags'] = implode(',', $updatedCustomer['tags']);
        $updatedCustomer['id'] = $customer['id'];
        $updatedCustomer['vat'] = strtoupper($updatedCustomer['vat']);

        $response = $this->post('/api/customer', $updatedCustomer);

        $customer['tags'] = explode(',', $customer['tags']);
        $updatedCustomer['tags'] = explode(',', $updatedCustomer['tags']);

        $response->assertJsonFragment([
            'customer' => [
                'id' => $customer['id'],
            ],
        ]);

        $this->assertEquals(array_except(Customer::find($customer['id'])->toArray(), ['seller', 'industry', 'country']), $updatedCustomer);
        $this->assertNotEquals(array_except(Customer::find($customer['id'])->toArray(), ['seller', 'industry', 'country']), $customer);
    }
}
