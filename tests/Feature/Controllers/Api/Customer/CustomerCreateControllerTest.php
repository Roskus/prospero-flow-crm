<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Customer;

use App\Models\Customer;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CustomerCreateControllerTest extends TestCase
{
    #[Test]
    public function it_can_save_customer(): void
    {
        $this->actingAs(User::factory()->create(), 'api');

        $data = array_except(Customer::factory()->create(['seller_id' => auth()->id()])->toArray(), 'id');
        $data['tags'] = implode(',', $data['tags']);

        $response = $this->post('/api/customer', $data);

        $response->assertJsonFragment([
            'customer' => [
                'id' => Customer::all()->last()->id,
            ],
        ]);

        $this->equalTo(Customer::all()->last(), $data);
    }
}
