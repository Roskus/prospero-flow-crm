<?php

namespace Tests\Feature\Controllers\Customer;

use App\Models\Customer;
use Tests\TestCase;

class CustomerDeleteControllerTest extends TestCase
{
    /** @test */
    public function it_can_delete_customer()
    {
        $customer = Customer::factory()->create();

        $response = $this->get('/customer/delete/'.$customer->id);

        $response->assertRedirect('/customer');
        $response->assertSessionHas('message', 'Customer deleted successfully');
        $this->assertDatabaseMissing('customer', $customer->toArray());
    }
}
