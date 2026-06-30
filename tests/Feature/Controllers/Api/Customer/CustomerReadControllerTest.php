<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Customer;

use App\Models\Customer;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CustomerReadControllerTest extends TestCase
{
    #[Test]
    public function it_can_read_customer(): void
    {
        $this->actingAs($this->user, 'api');
        $customer = Customer::factory()->create(['company_id' => auth()->user()->company_id, 'seller_id' => auth()->id()]);

        $response = $this->get('/api/customer/'.$customer->id);

        $responseCustomer = array_except($response->json()['customer'], ['seller', 'industry', 'country', 'company']);
        $customer->refresh();
        $expected = array_intersect_key($customer->toArray(), $responseCustomer);
        $this->assertEquals($expected, $responseCustomer);
    }

    #[Test]
    public function it_not_found_a_customer(): void
    {
        $this->actingAs($this->user, 'api');

        $response = $this->get('/api/customer/9999');

        $response->assertNotFound();
        $response->assertExactJson(['customer' => null]);
    }
}
