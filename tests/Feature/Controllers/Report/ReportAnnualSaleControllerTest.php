<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Report;

use App\Http\Requests\ReportAnnualSaleRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReportAnnualSaleControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo(Carbon::parse('2026-10-15 12:00'));
        $this->user->company->update(['currency' => 'EUR']);
    }

    #[Test]
    public function the_page_shows_the_monthly_table_and_the_chart_data(): void
    {
        $this->sale('2026-03-05', 1500);
        $this->sale('2025-03-10', 1000);

        $response = $this->get('/report/sale/annual');

        $response->assertOk()
            ->assertViewHas('year', 2026)
            ->assertSee('id="annual-sales-chart"', false)
            ->assertSee('chart.js@4.4.1', false)
            ->assertSee('integrity="sha384-', false)
            ->assertSee('+50.0%')
            ->assertSee('class="table table-sm table-striped annual-months"', false);

        preg_match('/<script type="application\/json" id="annual-sales-data">(.*?)<\/script>/s', $response->getContent(), $matches);
        $chart = json_decode($matches[1], true);
        $this->assertCount(12, $chart['labels']);
        $this->assertSame('2026', $chart['series'][0]['label']);
        $this->assertEquals(1500, $chart['series'][0]['data'][2]);
        $this->assertEquals(1000, $chart['series'][1]['data'][2]);
    }

    #[Test]
    public function another_year_can_be_chosen(): void
    {
        $this->sale('2024-05-01', 300);

        $this->get('/report/sale/annual?year=2025')
            ->assertOk()
            ->assertViewHas('year', 2025)
            ->assertViewHas('report', fn (array $report) => $report['previous_total'] === 300.0);
    }

    #[Test]
    public function an_invalid_year_is_rejected(): void
    {
        $this->get('/report/sale/annual?year=1990')->assertSessionHasErrors('year');
        $this->get('/report/sale/annual?year=2030')->assertSessionHasErrors('year');
    }

    #[Test]
    public function a_seller_only_sees_their_own_sales(): void
    {
        $this->seed([RoleSeeder::class, PermissionSeeder::class]);
        $seller = User::factory()->create(['company_id' => $this->user->company_id, 'first_name' => 'Sara', 'last_name' => 'Vende']);
        $seller->assignRole('Seller');
        $this->sale('2026-04-01', 200, $seller);
        $this->sale('2026-04-02', 900);

        $this->actingAs($seller)->get('/report/sale/annual?seller_id='.$this->user->id)
            ->assertOk()
            ->assertViewHas('report', fn (array $report) => $report['total'] === 200.0 && count($report['sellers']) === 1)
            ->assertSee('Sara Vende')
            ->assertDontSee('name="seller_id"', false);
    }

    #[Test]
    public function a_user_without_the_sales_report_permission_is_forbidden(): void
    {
        $user = User::factory()->create(['company_id' => $this->user->company_id]);

        $this->actingAs($user)->get('/report/sale/annual')->assertForbidden();
        $this->actingAs($user)->get('/report/sale/annual/pdf')->assertForbidden();
    }

    #[Test]
    public function the_pdf_has_the_tables_and_totals_of_the_year(): void
    {
        $this->sale('2026-03-05', 1500);

        $html = view('report.annual_sale_pdf', $this->app->make(ReportAnnualSaleRequest::class)->annualReportData())->render();

        $this->assertStringContainsString(__('Annual sales report').' 2026', $html);
        $this->assertStringContainsString('annual-months', $html);
        $this->assertStringContainsString(__('Top products'), $html);
        $this->assertStringNotContainsString('<canvas', $html);
    }

    #[Test]
    public function the_pdf_is_downloaded(): void
    {
        $response = $this->get('/report/sale/annual/pdf?year=2026');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $response->assertHeader('Content-Disposition', 'attachment; filename="annual_sales_report_2026.pdf"');
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    private function sale(string $date, float $amount, ?User $seller = null): void
    {
        Order::factory()->create([
            'company_id' => $this->user->company_id,
            'customer_id' => Customer::factory()->create(['company_id' => $this->user->company_id])->id,
            'seller_id' => ($seller ?? $this->user)->id,
            'status' => Order::CONFIRMED,
            'currency' => 'EUR',
            'order_date' => $date,
            'amount' => $amount,
        ]);
    }
}
