<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Customer;

use App\Models\Customer;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CustomerShowControllerTest extends TestCase
{
    #[Test]
    public function it_blocks_unauthenticated_access(): void
    {
        auth()->guard('web')->logout();

        $response = $this->get('/customer/show/1');

        $response->assertRedirect('/login');
    }

    #[Test]
    public function it_can_show_customer(): void
    {
        $customer = Customer::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->get('/customer/show/'.$customer->id);

        $response->assertOk();
        $response->assertViewIs('lead_customer.show');
        $response->assertSee($customer->name);
        $response->assertSee($customer->business_name);
        $response->assertSee($customer->phone);
        $response->assertSee(__('Contacts'));
    }

    #[Test]
    public function it_blocks_showing_another_companys_customer(): void
    {
        $customer = Customer::factory()->create(['company_id' => $this->user->company_id + 1]);

        $response = $this->get('/customer/show/'.$customer->id);

        $response->assertNotFound();
    }
}
