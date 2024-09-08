<?php

namespace Tests\Feature\Controllers\Customer;

use App\Models\Customer;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CustomerIndexControllerTest extends TestCase
{
    #[Test]
    public function it_can_view_index_customers()
    {
        $customer = Customer::factory()->create(['company_id' => $this->user->company_id, 'status' => Customer::OPEN]);
        $customer2 = Customer::factory()->create(['company_id' => $this->user->company_id, 'status' => Customer::CLOSED]);

        $response = $this->get('/customer');
        $response->assertOk();
        $response->assertSee($customer->name);
        $response->assertSee($customer2->name);

        // SEARCH
        $response = $this->get('/customer?search='.$customer->name);
        $response->assertSee($customer->name);
        $response->assertDontSee($customer2->name);

        // FILTERS
        $response = $this->get('/customer?country_id='.$customer->country_id);
        $response->assertSee($customer->name);
        $response->assertDontSee($customer2->name);

        $response = $this->get('/customer?seller_id='.$customer->seller_id);
        $response->assertSee($customer->name);
        $response->assertDontSee($customer2->name);

        $response = $this->get('/customer?status='.$customer->status);
        $response->assertSee($customer->name);
        $response->assertDontSee($customer2->name);

        $response = $this->get('/customer?industry_id='.$customer->industry->id);
        $response->assertSee($customer->name);
        $response->assertDontSee($customer2->name);
    }
}
