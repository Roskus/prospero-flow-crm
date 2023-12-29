<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Customer;

use App\Models\Customer;
use Tests\TestCase;

class CustomerSaveControllerTest extends TestCase
{
    /** @test */
    public function it_can_save_customer(): void
    {
        $data = Customer::factory()->create()->toArray();
        $data['tags'] = 'one, two';

        $response = $this->post('customer/save', $data);

        $response->assertRedirect('/customer');
        $this->equalTo(Customer::all()->last(), $data);
    }

    /** @test */
    public function it_can_update_customer(): void
    {
        $data = Customer::factory()->create()->toArray();
        $data['tags'] = 'one, two';
        unset($data['id']);

        $response = $this->post('customer/save', $data);

        $response->assertRedirect('/customer');
        $this->equalTo(Customer::all()->last(), $data);
    }
}
