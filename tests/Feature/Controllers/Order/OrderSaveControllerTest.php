<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Order;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderSaveControllerTest extends TestCase
{
    #[Test]
    public function it_blocks_unauthenticated_order_save(): void
    {
        auth()->guard('web')->logout();

        $response = $this->post('/order/save', []);

        $response->assertRedirect(route('login'));
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

    #[Test]
    public function it_rejects_updating_order_from_other_company(): void
    {
        $otherCompany = Company::factory()->create();
        $victimOrder = Order::factory()->create([
            'company_id' => $otherCompany->id,
        ]);
        $customer = Customer::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->post('/order/save', [
            'id' => $victimOrder->id,
            'customer_id' => $customer->id,
        ]);

        $response->assertStatus(404);
        $this->assertDatabaseHas('order', [
            'id' => $victimOrder->id,
            'company_id' => $otherCompany->id,
        ]);
    }

    #[Test]
    public function it_accepts_updating_order_from_own_company(): void
    {
        $order = Order::factory()->create([
            'company_id' => $this->user->company_id,
        ]);
        $customer = Customer::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->post('/order/save', [
            'id' => $order->id,
            'customer_id' => $customer->id,
        ]);

        $response->assertRedirect('order');
        $this->assertDatabaseHas('order', [
            'id' => $order->id,
            'company_id' => $this->user->company_id,
            'customer_id' => $customer->id,
        ]);
    }

    #[Test]
    public function it_rejects_customer_from_other_company(): void
    {
        $otherCompany = Company::factory()->create();
        $customer = Customer::factory()->create(['company_id' => $otherCompany->id]);

        $response = $this->post('/order/save', [
            'customer_id' => $customer->id,
        ]);

        $response->assertStatus(404);
    }

    #[Test]
    public function the_order_form_defaults_the_date_to_today(): void
    {
        $this->travelTo(Carbon::parse('2026-10-27 15:30:00'));

        $response = $this->get('/order/create');

        $response->assertOk();
        $response->assertSee('name="order_date"', false);
        $response->assertSee('value="2026-10-27"', false);
    }

    #[Test]
    public function the_order_form_shows_the_saved_date_when_editing(): void
    {
        $order = Order::factory()->create([
            'company_id' => $this->user->company_id,
            'order_date' => '2024-03-15',
        ]);

        $response = $this->get('/order/update/'.$order->order_number);

        $response->assertOk();
        $response->assertSee('value="2024-03-15"', false);
    }

    #[Test]
    public function it_saves_a_new_order_with_a_historical_date_without_touching_created_at(): void
    {
        $this->travelTo(Carbon::parse('2026-10-27 15:30:00'));
        $customer = Customer::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->post('/order/save', [
            'customer_id' => $customer->id,
            'order_date' => '2024-03-15',
        ]);

        $response->assertRedirect('order');
        $order = Order::where('customer_id', $customer->id)->firstOrFail();
        $this->assertSame('2024-03-15', $order->order_date->toDateString());
        $this->assertSame('2026-10-27', $order->created_at->toDateString());
    }

    #[Test]
    public function it_defaults_the_order_date_to_today_when_not_given(): void
    {
        $this->travelTo(Carbon::parse('2026-10-27 15:30:00'));
        $customer = Customer::factory()->create(['company_id' => $this->user->company_id]);

        $this->post('/order/save', ['customer_id' => $customer->id])->assertRedirect('order');

        $order = Order::where('customer_id', $customer->id)->firstOrFail();
        $this->assertSame('2026-10-27', $order->order_date->toDateString());
    }

    #[Test]
    public function it_changes_the_date_of_an_existing_order(): void
    {
        $order = Order::factory()->create(['company_id' => $this->user->company_id]);
        $customer = Customer::factory()->create(['company_id' => $this->user->company_id]);

        $this->post('/order/save', [
            'id' => $order->id,
            'customer_id' => $customer->id,
            'order_date' => '2023-12-31',
        ])->assertRedirect('order');

        $this->assertSame('2023-12-31', $order->fresh()->order_date->toDateString());
    }

    #[Test]
    public function it_keeps_the_order_date_when_updating_without_it(): void
    {
        $order = Order::factory()->create([
            'company_id' => $this->user->company_id,
            'customer_id' => Customer::factory()->create(['company_id' => $this->user->company_id])->id,
            'order_date' => '2023-12-31',
        ]);

        $this->post('/order/save', [
            'id' => $order->id,
            'customer_id' => $order->customer_id,
        ])->assertRedirect('order');

        $this->assertSame('2023-12-31', $order->fresh()->order_date->toDateString());
    }

    #[Test]
    public function it_rejects_an_invalid_date(): void
    {
        $customer = Customer::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->post('/order/save', [
            'customer_id' => $customer->id,
            'order_date' => '15/03/2024',
        ]);

        $response->assertSessionHasErrors('order_date');
    }

    #[Test]
    public function a_new_order_takes_its_creation_date_as_order_date(): void
    {
        $order = Order::factory()->create([
            'company_id' => $this->user->company_id,
            'created_at' => '2025-05-20 10:00:00',
        ]);

        $this->assertSame('2025-05-20', $order->order_date->toDateString());
    }

    #[Test]
    public function the_order_date_is_shown_on_the_order_and_the_pdf(): void
    {
        $order = Order::factory()->create([
            'company_id' => $this->user->company_id,
            'customer_id' => Customer::factory()->create(['company_id' => $this->user->company_id])->id,
            'seller_id' => $this->user->id,
            'order_date' => '2024-03-15',
        ]);

        $this->get('/order/show/'.$order->order_number)->assertOk()->assertSeeText('15/03/2024');
        $this->get('/order')->assertOk()->assertSeeText('15/03/2024');
        $this->assertStringContainsString('15/03/2024', view('order.pdf', ['order' => $order])->render());
    }
}
