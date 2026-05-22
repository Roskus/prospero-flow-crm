<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Customer;

use App\Models\Customer;
use App\Models\Customer\Message as CustomerMessage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CustomerMessageControllerTest extends TestCase
{
    #[Test]
    public function it_blocks_unauthenticated_message_save(): void
    {
        auth()->guard('web')->logout();

        $response = $this->post('/customer/message/save', []);

        $response->assertRedirect('/login');
    }

    #[Test]
    public function it_can_save_customer_message(): void
    {
        $customer = Customer::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->post('/customer/message/save', [
            'customer_id' => $customer->id,
            'message' => 'Test message',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('customer_message', [
            'customer_id' => $customer->id,
            'author_id' => $this->user->id,
            'body' => 'Test message',
        ]);
    }

    #[Test]
    public function it_blocks_unauthenticated_message_delete(): void
    {
        $customer = Customer::factory()->create(['company_id' => $this->user->company_id]);
        $message = CustomerMessage::create([
            'customer_id' => $customer->id,
            'author_id' => $this->user->id,
            'body' => 'Test',
        ]);

        auth()->guard('web')->logout();

        $response = $this->get("/customer/message/delete/{$message->id}");

        $response->assertRedirect('/login');
    }

    #[Test]
    public function it_blocks_deleting_another_users_message(): void
    {
        $customer = Customer::factory()->create(['company_id' => $this->user->company_id]);
        $otherMessage = CustomerMessage::create([
            'customer_id' => $customer->id,
            'author_id' => $this->user->id + 1,
            'body' => 'Other user message',
        ]);

        $response = $this->get("/customer/message/delete/{$otherMessage->id}");

        $response->assertNotFound();
    }

    #[Test]
    public function it_can_delete_own_message(): void
    {
        $customer = Customer::factory()->create(['company_id' => $this->user->company_id]);
        $message = CustomerMessage::create([
            'customer_id' => $customer->id,
            'author_id' => $this->user->id,
            'body' => 'Test',
        ]);

        $response = $this->get("/customer/message/delete/{$message->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('customer_message', ['id' => $message->id]);
    }
}
