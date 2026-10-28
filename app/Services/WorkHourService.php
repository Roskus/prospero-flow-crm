<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CompanyHoliday;
use App\Models\TimeOff;
use App\Models\User;
use App\Models\WorkHour;
use App\Models\WorkSchedule;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Worked and expected hours of employees, shared by the time entries screen and the work hours report.
 */
class WorkHourService
{
    /**
     * Days longer than this without a break lose one hour, in worked and in expected hours.
     */
    private const int BREAK_THRESHOLD_MINUTES = 6 * 60;

    private const int BREAK_MINUTES = 60;

    public const string TIME_OFF_APPROVED = 'approved';

    /**
     * Hours of each day of the range for one employee.
     *
     * @return list<array{date: Carbon, entries: Collection<int, WorkHour>, total: float, gross_total: float, estimated: float, is_holiday: bool, holiday_name: ?string, is_time_off: bool, open_entries: int}>
     */
    public function dailyHours(User $employee, CarbonInterface $from, CarbonInterface $to): array
    {
        $data = $this->load(collect([$employee]), $from, $to);

        return $this->days($employee, $data, $from, $to);
    }

    /**
     * One row per employee with the totals of the range; data is loaded with one query per table.
     *
     * @param  Collection<int, User>  $employees
     * @return list<array{employee: User, worked: float, expected: float, difference: float, days_worked: int, open_entries: int}>
     */
    public function summary(Collection $employees, CarbonInterface $from, CarbonInterface $to): array
    {
        $data = $this->load($employees, $from, $to);

        return $employees->map(function (User $employee) use ($data, $from, $to): array {
            $days = $this->days($employee, $data, $from, $to);
            $worked = round(array_sum(array_column($days, 'total')), 2);
            $expected = round(array_sum(array_column($days, 'estimated')), 2);

            return [
                'employee' => $employee,
                'worked' => $worked,
                'expected' => $expected,
                'difference' => round($worked - $expected, 2),
                'days_worked' => count(array_filter($days, fn (array $day): bool => $day['total'] > 0)),
                'open_entries' => array_sum(array_column($days, 'open_entries')),
            ];
        })->values()->all();
    }

    /**
     * @param  Collection<int, User>  $employees
     * @return array{entries: Collection<int|string, Collection<int, WorkHour>>, schedules: Collection<int|string, Collection<int, WorkSchedule>>, holidays: Collection<int|string, Collection<string, CompanyHoliday>>, timeOffs: Collection<int|string, Collection<int, TimeOff>>}
     */
    private function load(Collection $employees, CarbonInterface $from, CarbonInterface $to): array
    {
        $employeeIds = $employees->pluck('id')->all();
        $companyIds = $employees->pluck('company_id')->unique()->all();

        return [
            'entries' => WorkHour::query()
                ->whereIn('user_id', $employeeIds)
                ->whereBetween('start_time', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
                ->orderBy('start_time')
                ->get()
                ->toBase()
                ->groupBy('user_id'),
            'schedules' => WorkSchedule::query()->whereIn('user_id', $employeeIds)->get()->toBase()->groupBy('user_id'),
            'holidays' => CompanyHoliday::query()
                ->whereIn('company_id', $companyIds)
                ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
                ->get()
                ->toBase()
                ->groupBy('company_id')
                ->map(fn (Collection $holidays): Collection => $holidays->keyBy(fn (CompanyHoliday $holiday): string => $holiday->date->toDateString())),
            'timeOffs' => TimeOff::query()
                ->whereIn('user_id', $employeeIds)
                ->where('status', self::TIME_OFF_APPROVED)
                ->whereDate('start_date', '<=', $to->toDateString())
                ->whereDate('end_date', '>=', $from->toDateString())
                ->get()
                ->toBase()
                ->groupBy('user_id'),
        ];
    }

    /**
     * @param  array{entries: Collection<int|string, Collection<int, WorkHour>>, schedules: Collection<int|string, Collection<int, WorkSchedule>>, holidays: Collection<int|string, Collection<string, CompanyHoliday>>, timeOffs: Collection<int|string, Collection<int, TimeOff>>}  $data
     * @return list<array{date: Carbon, entries: Collection<int, WorkHour>, total: float, gross_total: float, estimated: float, is_holiday: bool, holiday_name: ?string, is_time_off: bool, open_entries: int}>
     */
    private function days(User $employee, array $data, CarbonInterface $from, CarbonInterface $to): array
    {
        $entries = $data['entries']->get($employee->id, collect());
        $blocksByWeekday = $data['schedules']->get($employee->id, collect())->groupBy('day_of_week');
        $holidays = $data['holidays']->get($employee->company_id, collect());
        $timeOffs = $data['timeOffs']->get($employee->id, collect());

        $days = [];
        foreach (CarbonPeriod::create($from->copy()->startOfDay(), $to->copy()->startOfDay()) as $date) {
            $date = Carbon::instance($date);
            $dateKey = $date->toDateString();
            $dayEntries = $entries->filter(fn (WorkHour $entry): bool => $entry->start_time->toDateString() === $dateKey)->values();

            $grossMinutes = 0;
            $hasBreak = false;
            foreach ($dayEntries as $entry) {
                if ($entry->end_time !== null) {
                    $grossMinutes += (int) $entry->start_time->diffInMinutes($entry->end_time);
                    $hasBreak = $hasBreak || $entry->type === 'break';
                }
            }

            $holiday = $holidays->get($dateKey);
            $isTimeOff = $timeOffs->contains(fn (TimeOff $timeOff): bool => $timeOff->start_date->toDateString() <= $dateKey && $timeOff->end_date->toDateString() >= $dateKey);

            $days[] = [
                'date' => $date,
                'entries' => $dayEntries,
                'total' => round(self::withBreakRule($grossMinutes, $hasBreak) / 60, 2),
                'gross_total' => round($grossMinutes / 60, 2),
                'estimated' => $holiday !== null || $isTimeOff ? 0.0 : round($this->expectedMinutes($blocksByWeekday->get($date->dayOfWeekIso, collect())) / 60, 2),
                'is_holiday' => $holiday !== null,
                'holiday_name' => $holiday?->name,
                'is_time_off' => $isTimeOff,
                'open_entries' => $dayEntries->whereNull('end_time')->count(),
            ];
        }

        return $days;
    }

    /**
     * @param  Collection<int, WorkSchedule>  $blocks
     */
    private function expectedMinutes(Collection $blocks): int
    {
        $minutes = 0;
        $hasBreak = false;
        foreach ($blocks as $block) {
            if ($block->type === 'work') {
                $minutes += (int) Carbon::parse($block->start_time)->diffInMinutes(Carbon::parse($block->end_time));
            }
            $hasBreak = $hasBreak || $block->type === 'break';
        }

        return self::withBreakRule($minutes, $hasBreak);
    }

    private static function withBreakRule(int $minutes, bool $hasBreak): int
    {
        return $minutes > self::BREAK_THRESHOLD_MINUTES && ! $hasBreak ? max(0, $minutes - self::BREAK_MINUTES) : $minutes;
    }
}
