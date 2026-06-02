<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Customer;

use App\Models\Customer;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CustomerDeleteControllerTest extends TestCase
{
    #[Test]
    public function it_can_delete_a_customer(): void
    {
        $this->actingAs(User::factory()->create(), 'api');
        $customer = Customer::factory()->create(['company_id' => auth()->user()->company_id, 'seller_id' => auth()->id()]);

        $response = $this->deleteJson('/api/customer/'.$customer->id);

        $response->assertOk();
        $this->assertSoftDeleted($customer);
    }

    #[Test]
    public function it_returns_404_for_nonexistent_customer(): void
    {
        $this->actingAs(User::factory()->create(), 'api');

        $response = $this->deleteJson('/api/customer/99999');

        $response->assertNotFound();
    }

    #[Test]
    public function it_cannot_delete_a_customer_from_another_company(): void
    {
        $this->actingAs(User::factory()->create(), 'api');
        $otherUser = User::factory()->create();
        $otherCustomer = Customer::factory()->create(['company_id' => $otherUser->company_id, 'seller_id' => $otherUser->id]);

        $response = $this->deleteJson('/api/customer/'.$otherCustomer->id);

        $response->assertNotFound();
        $this->assertNotSoftDeleted($otherCustomer);
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->deleteJson('/api/customer/'.$customer->id);

        $response->assertUnauthorized();
    }
}
