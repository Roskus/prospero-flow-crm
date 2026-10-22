<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Order;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Order;
use App\Services\BranchService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderBranchTest extends TestCase
{
    #[Test]
    public function a_new_order_belongs_to_the_current_branch_of_the_user(): void
    {
        $north = $this->branch('Norte');
        $this->user->update(['default_branch_id' => $north->id]);
        $customer = $this->customer();

        $this->post('/order/save', ['customer_id' => $customer->id])->assertRedirect('order');

        $this->assertSame($north->id, $this->latestOrder()->branch_id);
    }

    #[Test]
    public function a_new_order_can_choose_another_branch_of_the_company(): void
    {
        $center = $this->branch('Centro');

        $this->post('/order/save', ['customer_id' => $this->customer()->id, 'branch_id' => $center->id])->assertRedirect('order');

        $this->assertSame($center->id, $this->latestOrder()->branch_id);
    }

    #[Test]
    public function a_branch_of_another_company_is_rejected_on_the_web(): void
    {
        $otherBranch = Branch::factory()->create();

        $this->post('/order/save', ['customer_id' => $this->customer()->id, 'branch_id' => $otherBranch->id])
            ->assertSessionHasErrors('branch_id');

        $this->assertSame(0, Order::where('company_id', $this->user->company_id)->count());
    }

    #[Test]
    public function editing_an_order_keeps_its_branch_when_none_is_sent(): void
    {
        $center = $this->branch('Centro');
        $order = Order::factory()->create(['company_id' => $this->user->company_id, 'branch_id' => $center->id]);

        $this->post('/order/save', ['id' => $order->id, 'customer_id' => $this->customer()->id])->assertRedirect('order');

        $this->assertSame($center->id, $order->fresh()->branch_id);
    }

    #[Test]
    public function orders_created_without_a_user_get_the_main_branch_of_their_company(): void
    {
        auth()->guard('web')->logout();

        $order = Order::factory()->create(['company_id' => $this->user->company_id]);

        $this->assertSame($this->user->company->mainBranch->id, $order->branch_id);
    }

    #[Test]
    public function orders_of_another_company_never_take_the_branch_of_the_user(): void
    {
        $this->user->update(['default_branch_id' => $this->branch('Norte')->id]);
        $otherOrder = Order::factory()->create();

        $this->assertSame($otherOrder->company->mainBranch->id, $otherOrder->branch_id);
    }

    #[Test]
    public function the_api_creates_orders_in_the_current_or_the_chosen_branch(): void
    {
        $this->actingAs($this->user, 'api');
        $center = $this->branch('Centro');
        $payload = ['customer_id' => $this->customer()->id, 'amount' => 10, 'currency' => 'EUR'];

        $this->postJson('/api/order', $payload)
            ->assertCreated()
            ->assertJsonPath('order.branch_id', $this->user->company->mainBranch->id);

        $this->postJson('/api/order', $payload + ['branch_id' => $center->id])
            ->assertCreated()
            ->assertJsonPath('order.branch_id', $center->id);
    }

    #[Test]
    public function the_api_rejects_a_branch_of_another_company(): void
    {
        $this->actingAs($this->user, 'api');
        $otherBranch = Branch::factory()->create();
        $order = Order::factory()->create(['company_id' => $this->user->company_id]);

        $this->postJson('/api/order', ['customer_id' => $this->customer()->id, 'amount' => 10, 'currency' => 'EUR', 'branch_id' => $otherBranch->id])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('branch_id');
        $this->putJson('/api/order/'.$order->id, ['branch_id' => $otherBranch->id])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('branch_id');
    }

    #[Test]
    public function the_api_updates_the_branch_of_an_order(): void
    {
        $this->actingAs($this->user, 'api');
        $center = $this->branch('Centro');
        $order = Order::factory()->create(['company_id' => $this->user->company_id]);

        $this->putJson('/api/order/'.$order->id, ['branch_id' => $center->id])
            ->assertOk()
            ->assertJsonPath('order.branch_id', $center->id);
    }

    #[Test]
    public function a_single_branch_company_sees_no_branch_select_column_or_filter(): void
    {
        Order::factory()->create(['company_id' => $this->user->company_id]);

        $this->get('/order/create')->assertOk()->assertDontSee('name="branch_id"', false);
        $this->get('/order')->assertOk()->assertDontSee('name="branch_id"', false)->assertDontSeeText('All branches');
    }

    #[Test]
    public function a_company_with_several_branches_sees_the_branch_and_can_filter_by_it(): void
    {
        $north = $this->branch('Norte');
        $mainOrder = Order::factory()->create(['company_id' => $this->user->company_id]);
        $northOrder = Order::factory()->create(['company_id' => $this->user->company_id, 'branch_id' => $north->id]);

        $this->get('/order/create')->assertOk()->assertSee('name="branch_id"', false)->assertSeeText('Norte');
        $this->get('/order')
            ->assertOk()
            ->assertSeeText('All branches')
            ->assertSee('order/show/'.$mainOrder->order_number, false)
            ->assertSee('order/show/'.$northOrder->order_number, false);

        $this->get('/order?branch_id='.$north->id)
            ->assertOk()
            ->assertSee('order/show/'.$northOrder->order_number, false)
            ->assertDontSee('order/show/'.$mainOrder->order_number, false);
    }

    #[Test]
    public function orders_of_a_deleted_branch_keep_showing_its_name(): void
    {
        $north = $this->branch('Sucursal Norte');
        $this->branch('Centro');
        Order::factory()->create(['company_id' => $this->user->company_id, 'branch_id' => $north->id]);

        app(BranchService::class)->delete($north);

        $this->get('/order')->assertOk()->assertSeeText('Sucursal Norte');
        $this->get('/order/create')->assertOk()->assertDontSeeText('Sucursal Norte');
    }

    #[Test]
    public function the_order_page_and_pdf_show_the_branch_when_the_company_has_several(): void
    {
        $north = $this->branch('Sucursal Norte');
        $order = Order::factory()->create(['company_id' => $this->user->company_id, 'branch_id' => $north->id]);

        $this->get('/order/show/'.$order->order_number)->assertOk()->assertSeeText($north->code.' - Sucursal Norte');
        $this->get('/order/download/'.$order->order_number)->assertOk();
        $this->assertStringContainsString($north->code.' - Sucursal Norte', view('order.pdf', ['order' => $order, 'showBranch' => true])->render());
    }

    #[Test]
    public function the_order_page_hides_the_branch_when_the_company_has_only_one(): void
    {
        $order = Order::factory()->create(['company_id' => $this->user->company_id]);

        $this->get('/order/show/'.$order->order_number)->assertOk()->assertDontSeeText('Branch:');
    }

    private function branch(string $name): Branch
    {
        return Branch::factory()->forCompany($this->user->company_id)->create(['name' => $name]);
    }

    private function customer(): Customer
    {
        return Customer::factory()->create(['company_id' => $this->user->company_id]);
    }

    private function latestOrder(): Order
    {
        return Order::where('company_id', $this->user->company_id)->latest('id')->firstOrFail();
    }
}
