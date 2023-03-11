<?php

namespace Tests\Feature\Controllers\Customer;

use Tests\TestCase;

class CustomerCreateControllerTest extends TestCase
{
    /** @test */
    public function it_can_create_customer()
    {
        $response = $this->get('/customer/create');

        $response->assertOk();
        $response->assertViewIs('customer.customer');
        $response->assertSee('Name');
        $response->assertSee('Phone');
        $response->assertSee('Country');
        $response->assertSee('Street');
        $response->assertSee('Facebook');
        $response->assertSee('Status');
        $response->assertSee('Seller');
    }
}
