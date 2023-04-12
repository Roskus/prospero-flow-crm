<?php

namespace Tests\Feature\Controllers\Api\Customer;

use App\Models\Customer;
use App\Models\User;
use Tests\TestCase;

class CustomerReadControllerTest extends TestCase
{
    /** @test */
    public function it_can_read_customer(): void
    {
        $this->actingAs(User::factory()->create(), 'api');
        $customer = Customer::factory()->create(['company_id' => auth()->user()->company_id, 'seller_id' => auth()->id()]);

        $response = $this->get('/api/customer/'.$customer->id);

        $this->assertEquals(array_except($response->json()['customer'], ['seller', 'industry', 'country']), $customer->toArray());
    }

    /** @test */
    public function it_not_found_a_customer(): void
    {
        $this->actingAs(User::factory()->create(), 'api');

        $response = $this->get('/api/customer/9999');

        $response->assertNotFound();
        $response->assertExactJson(['customer' => null]);
    }
}
