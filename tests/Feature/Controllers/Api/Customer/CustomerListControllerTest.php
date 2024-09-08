<?php

namespace Tests\Feature\Controllers\Api\Customer;

use App\Models\Customer;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CustomerListControllerTest extends TestCase
{
    #[Test]
    public function it_can_list_customer(): void
    {
        $this->actingAs(User::factory()->create(), 'api');
        $customers = Customer::factory()->count(2)->create(['company_id' => auth()->user()->company_id, 'seller_id' => auth()->id()]);

        $response = $this->get('/api/customer');

        $this->assertEquals($response->json()['count'], $customers->count());
        $this->assertEquals(array_except($response->json()['customers'][0], ['seller', 'industry', 'country']), $customers[0]->toArray());
        $this->assertEquals(array_except($response->json()['customers'][1], ['seller', 'industry', 'country']), $customers[1]->toArray());
    }
}
