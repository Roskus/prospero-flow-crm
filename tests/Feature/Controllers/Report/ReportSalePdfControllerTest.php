<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Report;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\View;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReportSalePdfControllerTest extends TestCase
{
    /**
     * @var array<string, mixed>
     */
    private array $pdfViewData = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo(Carbon::parse('2026-10-18 12:00:00'));

        View::composer('report.sale_pdf', function ($view): void {
            $this->pdfViewData = $view->getData();
        });
    }

    #[Test]
    public function it_downloads_the_sales_report_as_pdf(): void
    {
        $this->createOrder(['amount' => 250, 'status' => Order::COMPLETED, 'created_at' => '2026-10-05 10:00:00']);

        $response = $this->get('/report/sale/pdf?from=2026-10-01&to=2026-10-18');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $response->assertHeader('Content-Disposition', 'attachment; filename="sales_report_20261001_20261018.pdf"');
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    #[Test]
    public function pdf_uses_the_same_filters_and_kpis_as_the_report_page(): void
    {
        $seller = User::factory()->create(['company_id' => $this->user->company_id, 'first_name' => 'Ana', 'last_name' => 'López']);
        $this->createOrder(['amount' => 100, 'status' => Order::CONFIRMED, 'seller_id' => $seller->id, 'created_at' => '2026-10-05 10:00:00']);
        $this->createOrder(['amount' => 900, 'status' => Order::CONFIRMED, 'created_at' => '2026-10-05 10:00:00']);

        $this->get('/report/sale/pdf?from=2026-10-01&to=2026-10-18&seller_id='.$seller->id)->assertOk();

        $this->assertSame('2026-10-01', $this->pdfViewData['from']->toDateString());
        $this->assertSame('2026-10-18', $this->pdfViewData['to']->toDateString());
        $this->assertSame($seller->id, $this->pdfViewData['seller']->id);
        $this->assertSame(100.0, $this->pdfViewData['report']['current']['total']);
        $this->assertSame(['total', 'orders', 'average_ticket', 'pending_orders'], array_keys($this->pdfViewData['kpis']));
    }

    #[Test]
    public function pdf_template_renders_company_period_seller_and_values(): void
    {
        $seller = User::factory()->create(['company_id' => $this->user->company_id, 'first_name' => 'Ana', 'last_name' => 'López']);
        $this->createOrder(['amount' => 1234.5, 'status' => Order::COMPLETED, 'seller_id' => $seller->id, 'created_at' => '2026-10-05 10:00:00']);

        $this->get('/report/sale/pdf?from=2026-10-01&to=2026-10-18&seller_id='.$seller->id)->assertOk();
        $html = view('report.sale_pdf', $this->pdfViewData)->render();

        $this->assertStringContainsString(e($this->user->company->name), $html);
        $this->assertStringContainsString('01/10/2026', $html);
        $this->assertStringContainsString('18/10/2026', $html);
        $this->assertStringContainsString('Ana López', $html);
        $this->assertStringContainsString('1,234.50', $html);
        $this->assertStringContainsString(__('Average ticket'), $html);
    }

    #[Test]
    public function seller_pdf_only_includes_own_sales(): void
    {
        $role = Role::findOrCreate('Seller', 'web');
        $role->givePermissionTo(Permission::findOrCreate('read report', 'web'));
        $seller = User::factory()->create();
        $seller->assignRole($role);
        $otherSeller = User::factory()->create(['company_id' => $seller->company_id]);
        $this->createOrder(['company_id' => $seller->company_id, 'seller_id' => $seller->id, 'amount' => 100, 'status' => Order::CONFIRMED, 'created_at' => '2026-10-05 10:00:00']);
        $this->createOrder(['company_id' => $seller->company_id, 'seller_id' => $otherSeller->id, 'amount' => 700, 'status' => Order::CONFIRMED, 'created_at' => '2026-10-05 10:00:00']);

        $this->actingAs($seller)->get('/report/sale/pdf?seller_id='.$otherSeller->id)->assertOk();

        $this->assertSame($seller->id, $this->pdfViewData['seller']->id);
        $this->assertSame(100.0, $this->pdfViewData['report']['current']['total']);
    }

    #[Test]
    public function user_without_read_report_permission_cannot_download_the_pdf(): void
    {
        $user = User::factory()->create();
        $user->assignRole('User');

        $this->actingAs($user)->get('/report/sale/pdf')->assertForbidden();
    }

    #[Test]
    public function it_rejects_invalid_filters(): void
    {
        $response = $this->from('/report/sale')->get('/report/sale/pdf?from=2026-10-10&to=2026-10-01');

        $response->assertRedirect('/report/sale');
        $response->assertSessionHasErrors('to');
    }

    #[Test]
    public function report_page_links_to_the_pdf_with_the_current_filters(): void
    {
        $seller = User::factory()->create(['company_id' => $this->user->company_id]);

        $response = $this->get('/report/sale?from=2026-10-01&to=2026-10-18&seller_id='.$seller->id);

        $response->assertSee(url('/report/sale/pdf').'?from=2026-10-01&amp;to=2026-10-18&amp;seller_id='.$seller->id, false);
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
}
