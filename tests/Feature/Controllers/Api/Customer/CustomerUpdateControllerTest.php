<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Customer;

use App\Models\Customer;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CustomerUpdateControllerTest extends TestCase
{
    #[Test]
    public function it_can_update_customer(): void
    {
        $this->actingAs($this->user, 'api');

        $customer = Customer::factory()->create(['seller_id' => $this->user->id]);

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
}
