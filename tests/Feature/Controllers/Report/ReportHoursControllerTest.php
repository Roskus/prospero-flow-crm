<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Report;

use App\Models\Company;
use App\Models\User;
use App\Models\WorkHour;
use App\Models\WorkSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ReportHoursControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $ana;

    private User $bruno;

    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo(Carbon::parse('2026-10-15 10:00'));
        $this->user->update(['is_employee' => false]);
        $this->ana = $this->employee('Ana');
        $this->bruno = $this->employee('Bruno');
    }

    #[Test]
    public function the_current_month_is_the_default_period(): void
    {
        $this->get('/report/hours')
            ->assertOk()
            ->assertViewHas('from', fn (Carbon $from) => $from->toDateString() === '2026-10-01')
            ->assertViewHas('to', fn (Carbon $to) => $to->toDateString() === '2026-10-31')
            ->assertSee('Ana')
            ->assertSee('Bruno');
    }

    #[Test]
    public function overtime_is_shown_with_a_plus_sign(): void
    {
        WorkSchedule::create(['user_id' => $this->ana->id, 'day_of_week' => 1, 'start_time' => '09:00', 'end_time' => '13:00', 'type' => 'work']);
        $this->work($this->ana, '2026-10-05 09:00', '2026-10-05 15:00');

        $response = $this->get('/report/hours?month=2026-10');

        $row = collect($response->viewData('rows'))->firstWhere('employee.id', $this->ana->id);
        $this->assertSame(6.0, $row['worked']);
        $this->assertSame(16.0, $row['expected']);
        $this->assertSame(-10.0, $row['difference']);
        $response->assertSee('-10.00');

        $this->work($this->ana, '2026-10-12 08:00', '2026-10-12 22:00');
        $this->work($this->ana, '2026-10-13 08:00', '2026-10-13 22:00');
        $this->get('/report/hours?month=2026-10')->assertSee('+16.00')->assertSee('text-success', false);
    }

    #[Test]
    public function a_custom_range_is_used(): void
    {
        $this->work($this->ana, '2026-09-30 09:00', '2026-09-30 12:00');
        $this->work($this->ana, '2026-10-20 09:00', '2026-10-20 12:00');

        $response = $this->get('/report/hours?from=2026-09-30&to=2026-10-01');

        $this->assertSame(3.0, collect($response->viewData('rows'))->firstWhere('employee.id', $this->ana->id)['worked']);
    }

    #[Test]
    public function invalid_ranges_are_rejected(): void
    {
        $this->get('/report/hours?from=2026-10-10&to=2026-10-01')->assertSessionHasErrors('to');
        $this->get('/report/hours?from=2025-01-01&to=2026-01-02')->assertSessionHasErrors('to');
        $this->get('/report/hours?from=2025-10-01&to=2026-09-30')->assertSessionHasNoErrors();
    }

    #[Test]
    public function an_open_clock_in_counts_as_open_and_adds_no_hours(): void
    {
        WorkHour::create(['user_id' => $this->ana->id, 'start_time' => '2026-10-15 09:00', 'type' => 'work']);

        $row = collect($this->get('/report/hours')->viewData('rows'))->firstWhere('employee.id', $this->ana->id);

        $this->assertSame(0.0, $row['worked']);
        $this->assertSame(1, $row['open_entries']);
    }

    #[Test]
    public function the_report_filters_by_employee(): void
    {
        $this->get('/report/hours?employee_id='.$this->bruno->id)
            ->assertOk()
            ->assertViewHas('rows', fn (array $rows) => count($rows) === 1 && $rows[0]['employee']->is($this->bruno));
    }

    #[Test]
    public function employees_of_another_company_are_never_shown(): void
    {
        $foreign = User::factory()->create(['company_id' => Company::factory()->create()->id, 'is_employee' => true, 'first_name' => 'Foreigner']);
        $this->work($foreign, '2026-10-05 09:00', '2026-10-05 12:00');

        $this->get('/report/hours')->assertDontSee('Foreigner');
        $this->get('/report/hours?employee_id='.$foreign->id)
            ->assertOk()
            ->assertViewHas('rows', [])
            ->assertDontSee('Foreigner');
    }

    #[Test]
    public function an_employee_with_only_their_timesheet_sees_only_their_row(): void
    {
        $this->ana->givePermissionTo(Permission::findOrCreate('manage own timesheet', 'web'));

        $this->actingAs($this->ana)->get('/report/hours?employee_id='.$this->bruno->id)
            ->assertOk()
            ->assertViewHas('rows', fn (array $rows) => count($rows) === 1 && $rows[0]['employee']->is($this->ana))
            ->assertDontSee('Bruno')
            ->assertDontSee('name="employee_id"', false);
    }

    #[Test]
    public function a_user_without_permission_is_forbidden(): void
    {
        $user = User::factory()->create(['company_id' => $this->user->company_id]);

        $this->actingAs($user)->get('/report/hours')->assertForbidden();
        $this->actingAs($user)->get('/report/hours/csv')->assertForbidden();
    }

    #[Test]
    public function the_csv_has_a_line_per_employee_and_a_total(): void
    {
        $this->work($this->ana, '2026-10-05 09:00', '2026-10-05 12:00');
        $this->work($this->bruno, '2026-10-06 09:00', '2026-10-06 11:30');

        $response = $this->get('/report/hours/csv?month=2026-10');

        $response->assertOk();
        $response->assertHeader('Content-Disposition', 'attachment; filename=hours_20261001_20261031.csv');
        $lines = array_values(array_filter(explode("\n", $response->streamedContent())));
        $this->assertCount(4, $lines);
        $this->assertStringContainsString('Ana', $lines[1]);
        $this->assertStringContainsString('3.00', $lines[1]);
        $this->assertStringContainsString('2.50', $lines[2]);
        $this->assertStringContainsString(__('Total'), $lines[3]);
        $this->assertStringContainsString('5.50', $lines[3]);
    }

    #[Test]
    public function the_time_entries_week_and_the_report_show_the_same_totals(): void
    {
        WorkSchedule::create(['user_id' => $this->ana->id, 'day_of_week' => 2, 'start_time' => '09:00', 'end_time' => '17:00', 'type' => 'work']);
        $this->work($this->ana, '2026-10-13 09:00', '2026-10-13 18:00');
        $this->work($this->ana, '2026-10-14 09:00', '2026-10-14 13:00');

        $week = $this->get('/rrhh/time-entries?user_id='.$this->ana->id);
        $report = $this->get('/report/hours?from=2026-10-12&to=2026-10-18&employee_id='.$this->ana->id);
        $row = $report->viewData('rows')[0];

        $this->assertSame($week->viewData('weekly_total'), $row['worked']);
        $this->assertSame($week->viewData('weekly_estimated'), $row['expected']);
        $this->assertSame(12.0, $row['worked']);
    }

    private function employee(string $firstName): User
    {
        return User::factory()->create(['company_id' => $this->user->company_id, 'is_employee' => true, 'first_name' => $firstName, 'last_name' => 'Test']);
    }

    private function work(User $employee, string $start, string $end): void
    {
        WorkHour::create(['user_id' => $employee->id, 'start_time' => $start, 'end_time' => $end, 'type' => 'work']);
    }
}
