<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Customer;

use App\Models\Customer;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CustomerDeleteControllerTest extends TestCase
{
    #[Test]
    public function it_can_delete_customer(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->get('/customer/delete/'.$customer->id);

        $response->assertRedirect('/customer');
        $response->assertSessionHas('message', __('Customer deleted successfully'));
        $this->assertDatabaseMissing('customer', $customer->toArray());
    }
}
