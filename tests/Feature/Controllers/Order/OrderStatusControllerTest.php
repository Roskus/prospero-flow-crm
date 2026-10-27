<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Order;

use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class OrderStatusControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_confirmed_order_goes_back_to_pending(): void
    {
        $order = $this->createOrder(Order::CONFIRMED);

        $response = $this->post(route('order.pending', $order->order_number));

        $response->assertRedirect('order');
        $this->assertSame(Order::PENDING, (int) $order->fresh()->status);
    }

    #[Test]
    public function only_confirmed_orders_go_back_to_pending(): void
    {
        $order = $this->createOrder(Order::COMPLETED);

        $response = $this->post(route('order.pending', $order->order_number));

        $response->assertRedirect('order');
        $response->assertSessionHas('error', __('Only confirmed orders can go back to pending'));
        $this->assertSame(Order::COMPLETED, (int) $order->fresh()->status);
    }

    #[Test]
    public function an_order_of_another_company_cannot_go_back_to_pending(): void
    {
        $order = Order::factory()->create([
            'company_id' => Company::factory()->create()->id,
            'status' => Order::CONFIRMED,
        ]);

        $response = $this->post(route('order.pending', $order->order_number));

        $response->assertNotFound();
        $this->assertSame(Order::CONFIRMED, (int) $order->fresh()->status);
    }

    #[Test]
    public function a_user_without_update_order_cannot_set_an_order_back_to_pending(): void
    {
        $user = User::factory()->create(['company_id' => $this->user->company_id]);
        $user->givePermissionTo(Permission::findOrCreate('read order', 'web'));
        $order = $this->createOrder(Order::CONFIRMED);

        $response = $this->actingAs($user)->post(route('order.pending', $order->order_number));

        $response->assertForbidden();
        $this->assertSame(Order::CONFIRMED, (int) $order->fresh()->status);
    }

    #[Test]
    public function the_back_to_pending_button_is_shown_only_for_confirmed_orders(): void
    {
        $confirmed = $this->createOrder(Order::CONFIRMED);
        $pending = $this->createOrder(Order::PENDING);

        $response = $this->get('/order');

        $response->assertOk();
        $response->assertSee(route('order.pending', $confirmed->order_number));
        $response->assertDontSee(route('order.pending', $pending->order_number));
        $response->assertSee(route('order.confirm', $pending->order_number));
        $response->assertDontSee(route('order.confirm', $confirmed->order_number));
    }

    #[Test]
    public function pending_and_confirmed_orders_can_be_canceled(): void
    {
        $pending = $this->createOrder(Order::PENDING);
        $confirmed = $this->createOrder(Order::CONFIRMED);

        $this->post(route('order.cancel', $pending->order_number))->assertRedirect('order');
        $this->post(route('order.cancel', $confirmed->order_number))->assertRedirect('order');

        $this->assertSame(Order::CANCELED, (int) $pending->fresh()->status);
        $this->assertSame(Order::CANCELED, (int) $confirmed->fresh()->status);
    }

    #[Test]
    public function completed_and_canceled_orders_cannot_be_canceled(): void
    {
        $completed = $this->createOrder(Order::COMPLETED);
        $canceled = $this->createOrder(Order::CANCELED);

        $this->post(route('order.cancel', $completed->order_number))
            ->assertSessionHas('error', __('Only pending or confirmed orders can be canceled'));
        $this->post(route('order.cancel', $canceled->order_number))
            ->assertSessionHas('error', __('Only pending or confirmed orders can be canceled'));

        $this->assertSame(Order::COMPLETED, (int) $completed->fresh()->status);
        $this->assertSame(Order::CANCELED, (int) $canceled->fresh()->status);
    }

    #[Test]
    public function an_order_of_another_company_cannot_be_canceled(): void
    {
        $order = Order::factory()->create([
            'company_id' => Company::factory()->create()->id,
            'status' => Order::PENDING,
        ]);

        $this->post(route('order.cancel', $order->order_number))->assertNotFound();

        $this->assertSame(Order::PENDING, (int) $order->fresh()->status);
    }

    #[Test]
    public function a_user_without_update_order_cannot_cancel_an_order(): void
    {
        $user = User::factory()->create(['company_id' => $this->user->company_id]);
        $user->givePermissionTo(Permission::findOrCreate('read order', 'web'));
        $order = $this->createOrder(Order::PENDING);

        $this->actingAs($user)->post(route('order.cancel', $order->order_number))->assertForbidden();

        $this->assertSame(Order::PENDING, (int) $order->fresh()->status);
    }

    #[Test]
    public function the_cancel_button_is_shown_only_for_pending_and_confirmed_orders(): void
    {
        $pending = $this->createOrder(Order::PENDING);
        $confirmed = $this->createOrder(Order::CONFIRMED);
        $completed = $this->createOrder(Order::COMPLETED);
        $canceled = $this->createOrder(Order::CANCELED);

        $response = $this->get('/order');

        $response->assertOk();
        $response->assertSee(route('order.cancel', $pending->order_number));
        $response->assertSee(route('order.cancel', $confirmed->order_number));
        $response->assertDontSee(route('order.cancel', $completed->order_number));
        $response->assertDontSee(route('order.cancel', $canceled->order_number));
    }

    #[Test]
    public function order_statuses_are_translated_to_spanish(): void
    {
        app()->setLocale('es');

        $this->assertSame('Pendiente', __((new Order(['status' => Order::PENDING]))->getStatusLabel()));
        $this->assertSame('Confirmado', __((new Order(['status' => Order::CONFIRMED]))->getStatusLabel()));
        $this->assertSame('Completado', __((new Order(['status' => Order::COMPLETED]))->getStatusLabel()));
        $this->assertSame('Cancelado', __((new Order(['status' => Order::CANCELED]))->getStatusLabel()));
    }

    private function createOrder(int $status): Order
    {
        return Order::factory()->create([
            'company_id' => $this->user->company_id,
            'status' => $status,
        ]);
    }
}
