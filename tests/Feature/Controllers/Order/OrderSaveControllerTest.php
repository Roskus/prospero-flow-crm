<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Order;

use App\Models\Customer;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderSaveControllerTest extends TestCase
{
    #[Test]
    public function it_blocks_unauthenticated_order_save(): void
    {
        auth()->guard('web')->logout();

        $response = $this->post('/order/save', []);

        $response->assertRedirect('/login');
    }

    #[Test]
    public function it_can_save_order(): void
    {
        $customer = Customer::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->post('/order/save', [
            'customer_id' => $customer->id,
            'currency' => 'USD',
        ]);

        $response->assertRedirect('order');
        $this->assertDatabaseHas('order', [
            'customer_id' => $customer->id,
            'company_id' => $this->user->company_id,
        ]);
    }
}
