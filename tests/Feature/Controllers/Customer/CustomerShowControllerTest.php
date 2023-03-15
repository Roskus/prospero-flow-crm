<?php

namespace Tests\Feature\Controllers\Customer;

use App\Models\Customer;
use Tests\TestCase;

class CustomerShowControllerTest extends TestCase
{
    /** @test */
    public function it_can_show_customer()
    {
        $customer = Customer::factory()->create();

        $response = $this->get('/customer/show/'.$customer->id);

        $response->assertOk();
        $response->assertViewIs('lead_customer.show');
        $response->assertSee($customer->name);
        $response->assertSee($customer->business_name);
        $response->assertSee($customer->phone);
        $response->assertSee('Contacts');
    }
}
