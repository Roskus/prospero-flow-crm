<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Models\Company;
use App\Models\CompanyHoliday;
use App\Models\TimeOff;
use App\Models\User;
use App\Models\WorkHour;
use App\Models\WorkSchedule;
use App\Services\WorkHourService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WorkHourServiceTest extends TestCase
{
    use RefreshDatabase;

    private WorkHourService $workHourService;

    private User $employee;

    private Carbon $monday;

    protected function setUp(): void
    {
        parent::setUp();

        $this->workHourService = app(WorkHourService::class);
        $this->employee = User::factory()->create(['company_id' => $this->user->company_id]);
        $this->monday = Carbon::parse('2026-10-26');
        WorkSchedule::create(['user_id' => $this->employee->id, 'day_of_week' => 1, 'start_time' => '09:00', 'end_time' => '17:00', 'type' => 'work']);
    }

    #[Test]
    public function a_long_day_without_a_break_loses_one_hour(): void
    {
        $this->entry('2026-10-26 09:00', '2026-10-26 17:00');

        $monday = $this->day('2026-10-26');

        $this->assertSame(8.0, $monday['gross_total']);
        $this->assertSame(7.0, $monday['total']);
        $this->assertSame(7.0, $monday['estimated']);
    }

    #[Test]
    public function a_day_with_a_break_keeps_every_hour(): void
    {
        $this->entry('2026-10-26 09:00', '2026-10-26 13:00');
        $this->entry('2026-10-26 13:00', '2026-10-26 13:30', 'break');
        $this->entry('2026-10-26 13:30', '2026-10-26 17:00');

        $this->assertSame(8.0, $this->day('2026-10-26')['total']);
    }

    #[Test]
    public function a_short_day_keeps_every_hour(): void
    {
        $this->entry('2026-10-26 09:00', '2026-10-26 14:30');

        $this->assertSame(5.5, $this->day('2026-10-26')['total']);
    }

    #[Test]
    public function a_holiday_expects_no_hours(): void
    {
        CompanyHoliday::create(['company_id' => $this->user->company_id, 'date' => '2026-10-26', 'name' => 'Fiesta']);

        $monday = $this->day('2026-10-26');

        $this->assertSame(0.0, $monday['estimated']);
        $this->assertTrue($monday['is_holiday']);
        $this->assertSame('Fiesta', $monday['holiday_name']);
    }

    #[Test]
    public function a_holiday_of_another_company_is_ignored(): void
    {
        CompanyHoliday::create(['company_id' => Company::factory()->create()->id, 'date' => '2026-10-26', 'name' => 'Other']);

        $this->assertSame(7.0, $this->day('2026-10-26')['estimated']);
    }

    #[Test]
    public function approved_time_off_expects_no_hours_and_pending_does(): void
    {
        $this->timeOff('2026-10-26', '2026-10-26', 'approved');
        $this->timeOff('2026-11-02', '2026-11-02', 'pending');

        $this->assertSame(0.0, $this->day('2026-10-26')['estimated']);
        $this->assertTrue($this->day('2026-10-26')['is_time_off']);
        $this->assertSame(7.0, $this->day('2026-11-02', '2026-11-02')['estimated']);
    }

    #[Test]
    public function an_open_entry_adds_no_hours(): void
    {
        WorkHour::create(['user_id' => $this->employee->id, 'start_time' => '2026-10-26 09:00', 'end_time' => null, 'type' => 'work']);

        $monday = $this->day('2026-10-26');

        $this->assertSame(0.0, $monday['total']);
        $this->assertSame(1, $monday['open_entries']);
    }

    #[Test]
    public function the_summary_adds_up_the_days_of_each_employee(): void
    {
        $this->entry('2026-10-26 09:00', '2026-10-26 17:00');
        $this->entry('2026-11-02 09:00', '2026-11-02 18:00');
        $colleague = User::factory()->create(['company_id' => $this->user->company_id]);
        WorkHour::create(['user_id' => $colleague->id, 'start_time' => '2026-10-27 09:00', 'end_time' => null, 'type' => 'work']);

        $rows = collect($this->workHourService->summary(collect([$this->employee, $colleague]), Carbon::parse('2026-10-26'), Carbon::parse('2026-11-08')));
        $row = $rows->firstWhere('employee.id', $this->employee->id);

        $this->assertSame(15.0, $row['worked']);
        $this->assertSame(14.0, $row['expected']);
        $this->assertSame(1.0, $row['difference']);
        $this->assertSame(2, $row['days_worked']);
        $this->assertSame(1, $rows->firstWhere('employee.id', $colleague->id)['open_entries']);
        $this->assertSame(0.0, $rows->firstWhere('employee.id', $colleague->id)['expected']);
    }

    /**
     * @return array<string, mixed>
     */
    private function day(string $date, string $from = '2026-10-26', string $to = '2026-11-01'): array
    {
        $days = $this->workHourService->dailyHours($this->employee, Carbon::parse($from), Carbon::parse($date > $to ? $date : $to));

        return collect($days)->first(fn (array $day): bool => $day['date']->toDateString() === $date);
    }

    private function entry(string $start, string $end, string $type = 'work'): void
    {
        WorkHour::create(['user_id' => $this->employee->id, 'start_time' => $start, 'end_time' => $end, 'type' => $type]);
    }

    private function timeOff(string $start, string $end, string $status): void
    {
        TimeOff::create(['company_id' => $this->user->company_id, 'user_id' => $this->employee->id, 'type' => 'vacation', 'start_date' => $start, 'end_date' => $end, 'days_used' => 1, 'status' => $status]);
    }
}
