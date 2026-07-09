<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Customer;

use App\Models\Customer;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CustomerSaveControllerTest extends TestCase
{
    #[Test]
    public function it_can_save_customer(): void
    {
        $data = [
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'phone' => '123456789',
            'country_id' => 'US',
            'seller_id' => $this->user->id,
            'tags' => 'one, two',
        ];

        $response = $this->post('/customer/save', $data);

        $response->assertRedirect('/customer');
        $this->assertDatabaseHas('customer', ['name' => 'Test Customer']);
    }

    #[Test]
    public function it_can_update_customer(): void
    {
        $customer = Customer::factory()->create([
            'company_id' => $this->user->company_id,
            'seller_id' => $this->user->id,
        ]);
        $data = $customer->toArray();
        $data['tags'] = 'one, two';
        $data['name'] = 'Updated Name';

        $response = $this->post('/customer/save', $data);

        $response->assertRedirect('/customer');
        $this->assertEquals('Updated Name', $customer->fresh()->name);
    }
}
