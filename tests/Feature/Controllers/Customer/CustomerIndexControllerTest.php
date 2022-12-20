<?php

namespace Tests\Feature\Controllers\Customer;

use App\Models\Customer;
use App\Models\Industry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerIndexControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_view_index_customers()
    {
        $user = $this->signIn();
        $this->actingAs($user);

        $user2 = User::factory()->create();
        $industry = Industry::factory()->create();
        $industry2 = Industry::factory()->create();
        $customer = Customer::factory()->create(['industry_id' => $industry->id, 'country_id' => 1, 'seller_id' => $user->id, 'status' => 'open']);
        $customer2 = Customer::factory()->create(['industry_id' => $industry2->id, 'country_id' => 2, 'seller_id' => $user2->id, 'status' => 'recall']);

        $response = $this->get('/customer');
        $response->assertOk();
        $response->assertSee($customer->name);
        $response->assertSee($customer2->name);

        // SEARCH
        $response = $this->get('/customer?search='.$customer->name);
        $response->assertSee($customer->name);
        $response->assertDontSee($customer2->name);

        // FILTERS
        $response = $this->get('/customer?country_id=1');
        $response->assertSee($customer->name);
        $response->assertDontSee($customer2->name);

        $response = $this->get('/customer?seller_id='.$user->id);
        $response->assertSee($customer->name);
        $response->assertDontSee($customer2->name);

        $response = $this->get('/customer?status=open');
        $response->assertSee($customer->name);
        $response->assertDontSee($customer2->name);

        $response = $this->get('/customer?industry_id='.$industry->id);
        $response->assertSee($customer->name);
        $response->assertDontSee($customer2->name);
    }
}
