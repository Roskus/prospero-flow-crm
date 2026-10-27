<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Dashboard;

use App\Models\Calendar;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Customer\Message;
use App\Models\Lead;
use App\Models\Lead\Message as LeadMessage;
use App\Models\Order;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardIndexControllerTest extends TestCase
{
    #[Test]
    public function it_shows_calendar_event_start_time_on_the_dashboard(): void
    {
        $this->user->update(['timezone' => 'UTC']);

        Calendar::factory()->create([
            'company_id' => $this->user->company_id,
            'user_id' => $this->user->id,
            'title' => 'Evento dashboard',
            'start_date' => now()->startOfMonth()->addDay()->setTime(20, 0, 0)->toDateTimeString(),
            'end_date' => now()->startOfMonth()->addDay()->setTime(21, 0, 0)->toDateTimeString(),
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSeeText('20:00 - Evento dashboard');
    }

    #[Test]
    public function it_shows_create_lead_button_on_the_dashboard(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('href="'.url('/lead/create').'"', false);
    }

    #[Test]
    public function it_hides_create_lead_button_without_permission(): void
    {
        $this->user->syncRoles(['User']);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertDontSee('href="'.url('/lead/create').'"', false);
    }

    #[Test]
    public function it_shows_create_customer_button_on_the_dashboard(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('href="'.url('/customer/create').'"', false);
    }

    #[Test]
    public function it_hides_create_customer_button_without_permission(): void
    {
        $this->user->syncRoles(['User']);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertDontSee('href="'.url('/customer/create').'"', false);
    }

    #[Test]
    public function it_shows_lead_country_flag_on_the_dashboard(): void
    {
        Lead::factory()->create([
            'company_id' => $this->user->company_id,
            'seller_id' => $this->user->id,
            'country_id' => 'es',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('title="Spain"', false);
        $response->assertSee('🇪🇸');
    }

    #[Test]
    public function it_lists_customers_by_latest_interaction(): void
    {
        $oldCustomer = Customer::factory()->create([
            'company_id' => $this->user->company_id,
            'name' => 'Old Customer',
            'updated_at' => now()->subDays(10),
        ]);
        $recentlyUpdatedCustomer = Customer::factory()->create([
            'company_id' => $this->user->company_id,
            'name' => 'Recently Updated Customer',
            'updated_at' => now()->subDay(),
        ]);
        $messagedCustomer = Customer::factory()->create([
            'company_id' => $this->user->company_id,
            'name' => 'Messaged Customer',
            'updated_at' => now()->subDays(20),
        ]);
        Message::withoutTouching(fn () => Message::create([
            'customer_id' => $messagedCustomer->id,
            'author_id' => $this->user->id,
            'body' => 'Follow up call',
        ]));
        $otherCompanyCustomer = Customer::factory()->create(['name' => 'Other Company Customer']);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSeeInOrder(['Messaged Customer', 'Recently Updated Customer', 'Old Customer']);
        $response->assertDontSee($otherCompanyCustomer->name);
        $response->assertSeeText(__('Updated at'));
        $response->assertSeeText($recentlyUpdatedCustomer->updated_at->format('d/m/Y H:i'));
    }

    #[Test]
    public function adding_a_message_updates_the_customer_updated_at(): void
    {
        $customer = Customer::factory()->create([
            'company_id' => $this->user->company_id,
            'updated_at' => now()->subDays(5),
        ]);

        Message::create([
            'customer_id' => $customer->id,
            'author_id' => $this->user->id,
            'body' => 'New note',
        ]);

        $this->assertTrue($customer->fresh()->updated_at->isToday());
    }

    #[Test]
    public function it_shows_customers_title(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSeeText(__('Customers'));
        $response->assertDontSeeText(__('Customers in progress'));
    }

    #[Test]
    public function it_shows_sales_summary_to_super_admin(): void
    {
        $this->travelTo(Carbon::parse('2026-10-15 12:00:00'));
        $this->createOrder(Order::CONFIRMED, 100.00, '2026-10-02');
        $this->createOrder(Order::COMPLETED, 50.00, '2026-10-10');
        $this->createOrder(Order::CANCELED, 999.00, '2026-10-11');
        $this->createOrder(Order::PENDING, 30.00, '2026-10-12');
        $this->createOrder(Order::COMPLETED, 100.00, '2026-09-20');

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSeeText(__('Sales this month'));
        $response->assertSeeText('150.00');
        $response->assertSeeText('+50%');
        $response->assertViewHas('sales', fn (array $sales) => $sales['current_month_total'] === 150.0
            && $sales['previous_month_total'] === 100.0
            && $sales['change_percentage'] === 50.0
            && $sales['current_month_orders'] === 2
            && $sales['pending_orders'] === 1);
    }

    #[Test]
    public function it_shows_sales_summary_to_company_admin(): void
    {
        Role::create(['name' => 'CompanyAdmin', 'guard_name' => 'web']);
        $this->user->syncRoles(['CompanyAdmin']);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertViewHas('sales');
        $response->assertSeeText(__('Sales this month'));
    }

    #[Test]
    public function it_hides_sales_summary_from_other_roles(): void
    {
        $this->user->syncRoles(['User']);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertViewMissing('sales');
        $response->assertDontSeeText(__('Sales this month'));
    }

    #[Test]
    public function it_shows_the_full_month_calendar_by_default(): void
    {
        $this->travelTo(Carbon::parse('2026-10-15 12:00:00'));

        $response = $this->get('/');

        $response->assertOk();
        $response->assertViewHas('calendarView', Company::CALENDAR_VIEW_MONTH);
        $response->assertViewHas('endOfCalendar', fn (Carbon $end) => $end->toDateString() === '2026-11-01');
    }

    #[Test]
    public function it_shows_a_weekly_calendar_when_the_company_is_configured(): void
    {
        $this->travelTo(Carbon::parse('2026-10-15 12:00:00'));
        Company::whereKey($this->user->company_id)->update(['dashboard_calendar_view' => Company::CALENDAR_VIEW_WEEK]);
        Calendar::factory()->create([
            'company_id' => $this->user->company_id,
            'user_id' => $this->user->id,
            'title' => 'Outside this week',
            'start_date' => '2026-10-02 10:00:00',
            'end_date' => '2026-10-02 11:00:00',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertViewHas('calendarView', Company::CALENDAR_VIEW_WEEK);
        $response->assertViewHas('endOfCalendar', fn (Carbon $end) => $end->toDateString() === '2026-10-18');
        $response->assertDontSeeText('Outside this week');
    }

    private function createOrder(int $status, float $amount, string $orderDate): Order
    {
        $order = Order::factory()->create([
            'company_id' => $this->user->company_id,
            'customer_id' => Customer::factory()->create(['company_id' => $this->user->company_id])->id,
            'status' => $status,
            'amount' => $amount,
            'order_date' => $orderDate,
        ]);

        return $order;
    }

    #[Test]
    public function it_lists_latest_updated_leads_with_updated_at(): void
    {
        $oldLead = Lead::factory()->create([
            'company_id' => $this->user->company_id,
            'seller_id' => $this->user->id,
            'name' => 'Old Lead',
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDays(10),
        ]);
        $recentlyUpdatedLead = Lead::factory()->create([
            'company_id' => $this->user->company_id,
            'seller_id' => $this->user->id,
            'name' => 'Recently Updated Lead',
            'created_at' => now()->subDays(30),
            'updated_at' => now()->subHour(),
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSeeInOrder(['Recently Updated Lead', 'Old Lead']);
        $response->assertSeeText($recentlyUpdatedLead->updated_at->format('d/m/Y H:i'));
        $response->assertDontSeeText($oldLead->created_at->format('d/m/Y H:i'));
    }

    #[Test]
    public function adding_a_message_updates_the_lead_updated_at(): void
    {
        $lead = Lead::factory()->create([
            'company_id' => $this->user->company_id,
            'seller_id' => $this->user->id,
            'updated_at' => now()->subDays(5),
        ]);

        LeadMessage::create([
            'lead_id' => $lead->id,
            'author_id' => $this->user->id,
            'body' => 'New note',
        ]);

        $this->assertTrue($lead->fresh()->updated_at->isToday());
    }

    #[Test]
    public function it_includes_the_quick_event_popup_for_users_who_can_create_events(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('id="quickEventModal"', false);
        $response->assertSee('id="dashboard-add-event"', false);
        $response->assertSee('QuickEvent.js', false);
        $response->assertSee("QuickEvent.open('".now()->toDateString()."')", false);
    }

    #[Test]
    public function it_hides_the_quick_event_popup_without_permission(): void
    {
        $this->user->syncRoles(['User']);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertDontSee('id="quickEventModal"', false);
        $response->assertDontSee('QuickEvent.open(', false);
    }

    #[Test]
    public function dashboard_events_link_to_the_full_calendar(): void
    {
        $this->travelTo(Carbon::parse('2026-10-15 12:00:00'));
        $event = Calendar::factory()->create([
            'company_id' => $this->user->company_id,
            'user_id' => $this->user->id,
            'start_date' => '2026-10-16 10:00:00',
            'end_date' => '2026-10-16 11:00:00',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee(url('/calendar/2026-10-16').'?event='.$event->id, false);
    }

    #[Test]
    public function quick_event_saved_from_the_dashboard_returns_to_the_dashboard(): void
    {
        $response = $this->from('/')->post(route('calendar.save'), [
            'title' => 'Quick call',
            'date' => '2026-10-20',
            'start_time' => '10:00',
            'end_time' => '10:30',
            'description' => 'From dashboard',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseHas('calendar', [
            'user_id' => $this->user->id,
            'title' => 'Quick call',
            'description' => 'From dashboard',
        ]);
    }

    #[Test]
    public function full_calendar_opens_the_linked_event(): void
    {
        $event = Calendar::factory()->create([
            'company_id' => $this->user->company_id,
            'user_id' => $this->user->id,
            'start_date' => '2026-10-16 10:00:00',
            'end_date' => '2026-10-16 11:00:00',
        ]);

        $response = $this->get('/calendar/2026-10-16?event='.$event->id);

        $response->assertOk();
        $response->assertSee('Calendar.read('.$event->id.');', false);
    }
}
