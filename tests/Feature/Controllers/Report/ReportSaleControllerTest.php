<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Report;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReportSaleControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo(Carbon::parse('2026-10-18 12:00:00'));
    }

    #[Test]
    public function it_shows_kpis_of_the_current_month_by_default(): void
    {
        $this->createOrder(['amount' => 100, 'status' => Order::CONFIRMED, 'created_at' => '2026-10-02 10:00:00']);
        $this->createOrder(['amount' => 300, 'status' => Order::COMPLETED, 'created_at' => '2026-10-15 10:00:00']);
        $this->createOrder(['amount' => 999, 'status' => Order::CANCELED, 'created_at' => '2026-10-10 10:00:00']);
        $this->createOrder(['amount' => 50, 'status' => Order::PENDING, 'created_at' => '2026-10-11 10:00:00']);
        $this->createOrder(['amount' => 5000, 'status' => Order::CONFIRMED, 'created_at' => '2026-09-20 10:00:00']);

        $response = $this->get('/report/sale');

        $response->assertOk();
        $response->assertViewHas('from', fn (Carbon $from) => $from->toDateString() === '2026-10-01');
        $response->assertViewHas('to', fn (Carbon $to) => $to->toDateString() === '2026-10-18');
        $response->assertViewHas('report', fn (array $report) => $report['current'] === [
            'total' => 400.0,
            'orders' => 2,
            'average_ticket' => 200.0,
            'pending_orders' => 1,
        ]);
    }

    #[Test]
    public function it_compares_with_the_previous_period_of_the_same_length(): void
    {
        $this->createOrder(['amount' => 300, 'status' => Order::CONFIRMED, 'created_at' => '2026-10-12 10:00:00']);
        $this->createOrder(['amount' => 100, 'status' => Order::CONFIRMED, 'created_at' => '2026-10-05 10:00:00']);
        $this->createOrder(['amount' => 100, 'status' => Order::CONFIRMED, 'created_at' => '2026-10-04 10:00:00']);

        $response = $this->get('/report/sale?from=2026-10-11&to=2026-10-17');

        $response->assertOk();
        $response->assertViewHas('report', function (array $report): bool {
            return $report['previous_from']->toDateString() === '2026-10-04'
                && $report['previous_to']->toDateString() === '2026-10-10'
                && $report['previous']['total'] === 200.0
                && $report['change']['total'] === 50.0
                && $report['change']['orders'] === -50.0;
        });
    }

    #[Test]
    public function it_has_no_change_percentage_when_the_previous_period_is_empty(): void
    {
        $this->createOrder(['amount' => 300, 'status' => Order::CONFIRMED, 'created_at' => '2026-10-12 10:00:00']);

        $response = $this->get('/report/sale?from=2026-10-11&to=2026-10-17');

        $response->assertViewHas('report', fn (array $report) => $report['change']['total'] === null);
    }

    #[Test]
    public function it_includes_orders_of_the_whole_last_day(): void
    {
        $this->createOrder(['amount' => 80, 'status' => Order::CONFIRMED, 'created_at' => '2026-10-17 23:59:00']);

        $response = $this->get('/report/sale?from=2026-10-17&to=2026-10-17');

        $response->assertViewHas('report', fn (array $report) => $report['current']['total'] === 80.0);
    }

    #[Test]
    public function it_excludes_orders_of_other_companies(): void
    {
        $this->createOrder(['amount' => 100, 'status' => Order::CONFIRMED, 'created_at' => '2026-10-05 10:00:00']);
        Order::factory()->create(['amount' => 9000, 'status' => Order::CONFIRMED, 'created_at' => '2026-10-05 10:00:00']);

        $response = $this->get('/report/sale');

        $response->assertViewHas('report', fn (array $report) => $report['current']['total'] === 100.0);
    }

    #[Test]
    public function company_admin_can_filter_by_seller(): void
    {
        $seller = User::factory()->create(['company_id' => $this->user->company_id]);
        $this->createOrder(['amount' => 100, 'status' => Order::CONFIRMED, 'seller_id' => $seller->id, 'created_at' => '2026-10-05 10:00:00']);
        $this->createOrder(['amount' => 700, 'status' => Order::CONFIRMED, 'created_at' => '2026-10-05 10:00:00']);

        $response = $this->get('/report/sale?seller_id='.$seller->id);

        $response->assertOk();
        $response->assertSee('id="seller_id"', false);
        $response->assertViewHas('report', fn (array $report) => $report['current']['total'] === 100.0);
    }

    #[Test]
    public function seller_only_sees_own_sales_even_when_requesting_another_seller(): void
    {
        $seller = $this->createSellerWithReportAccess();
        $otherSeller = User::factory()->create(['company_id' => $seller->company_id]);
        $this->createOrder(['company_id' => $seller->company_id, 'seller_id' => $seller->id, 'amount' => 100, 'status' => Order::CONFIRMED, 'created_at' => '2026-10-05 10:00:00']);
        $this->createOrder(['company_id' => $seller->company_id, 'seller_id' => $otherSeller->id, 'amount' => 700, 'status' => Order::CONFIRMED, 'created_at' => '2026-10-05 10:00:00']);

        $response = $this->actingAs($seller)->get('/report/sale?seller_id='.$otherSeller->id);

        $response->assertOk();
        $response->assertDontSee('id="seller_id"', false);
        $response->assertViewHas('report', fn (array $report) => $report['current']['total'] === 100.0);
    }

    #[Test]
    public function user_without_read_report_permission_is_forbidden(): void
    {
        $user = User::factory()->create();
        $user->assignRole('User');

        $this->actingAs($user)->get('/report/sale')->assertForbidden();
        $this->actingAs($user)->get('/report')->assertForbidden();
        $this->actingAs($user)->get('/report/email')->assertForbidden();
    }

    #[Test]
    public function it_rejects_an_end_date_before_the_start_date(): void
    {
        $response = $this->from('/report/sale')->get('/report/sale?from=2026-10-10&to=2026-10-01');

        $response->assertRedirect('/report/sale');
        $response->assertSessionHasErrors(['to' => __('The end date must be on or after the start date.')]);
    }

    #[Test]
    public function it_rejects_a_seller_from_another_company(): void
    {
        $foreignSeller = User::factory()->create();

        $response = $this->from('/report/sale')->get('/report/sale?seller_id='.$foreignSeller->id);

        $response->assertSessionHasErrors(['seller_id' => __('The selected seller is not valid.')]);
    }

    #[Test]
    public function user_menu_is_aligned_to_the_right_edge(): void
    {
        $response = $this->get('/report/sale');

        $response->assertSee('class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown10"', false);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createOrder(array $attributes): Order
    {
        return Order::factory()->create(array_merge([
            'company_id' => $this->user->company_id,
            'seller_id' => $this->user->id,
        ], $attributes));
    }

    private function createSellerWithReportAccess(): User
    {
        $role = Role::findOrCreate('Seller', 'web');
        $role->givePermissionTo(Permission::findOrCreate('read report', 'web'));

        $seller = User::factory()->create();
        $seller->assignRole($role);

        return $seller;
    }
}
