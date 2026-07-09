<?php

declare(strict_types=1);

namespace App\Http\Controllers\Rrhh\TimeEntry;

use App\Http\Controllers\MainController;
use App\Models\CompanyHoliday;
use App\Models\WorkHour;
use App\Models\WorkSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimeEntryIndexController extends MainController
{
    public function index(Request $request)
    {
        $employeeId = $request->get('user_id', Auth::user()->id);

        $weekOffset = (int) $request->get('week', 0);
        $startOfWeek = Carbon::now()->startOfWeek()->addWeeks($weekOffset);
        $endOfWeek = Carbon::now()->endOfWeek()->addWeeks($weekOffset);

        $entries = WorkHour::where('user_id', $employeeId)
            ->where('start_time', '>=', $startOfWeek)
            ->where('start_time', '<=', $endOfWeek)
            ->orderBy('start_time')
            ->get();

        $scheduleBlocks = WorkSchedule::where('user_id', $employeeId)->get()->groupBy('day_of_week');

        $holidays = CompanyHoliday::where('company_id', Auth::user()->company_id)
            ->whereBetween('date', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
            ->get()
            ->keyBy(function ($h) {
                return $h->date->format('Y-m-d');
            });

        $days = [];
        $weeklyTotal = 0;
        $weeklyEstimated = 0;

        for ($i = 0; $i < 7; $i++) {
            $date = (clone $startOfWeek)->addDays($i);
            $dayOfWeek = $date->dayOfWeekIso;

            $dayEntries = $entries->filter(function ($e) use ($date) {
                return $e->start_time->format('Y-m-d') === $date->format('Y-m-d');
            });

            $dayTotal = 0;
            $hasBreak = false;
            foreach ($dayEntries as $e) {
                if ($e->end_time) {
                    $minutes = $e->start_time->diffInMinutes($e->end_time);
                    $dayTotal += $minutes / 60;
                    if ($e->type === 'break') {
                        $hasBreak = true;
                    }
                }
            }

            $grossTotal = $dayTotal;
            if ($grossTotal > 6 && ! $hasBreak) {
                $dayTotal = max(0, $grossTotal - 1);
            }

            $blocks = $scheduleBlocks->get($dayOfWeek, collect());
            $estimated = 0;
            $hasScheduledBreak = false;
            foreach ($blocks as $block) {
                if ($block->type === 'work') {
                    $start = Carbon::parse($block->start_time);
                    $end = Carbon::parse($block->end_time);
                    $estimated += $start->diffInMinutes($end) / 60;
                }
                if ($block->type === 'break') {
                    $hasScheduledBreak = true;
                }
            }
            if ($estimated > 6 && ! $hasScheduledBreak) {
                $estimated = max(0, $estimated - 1);
            }

            $dateKey = $date->format('Y-m-d');
            $isHoliday = $holidays->has($dateKey);
            $holidayName = $isHoliday ? $holidays->get($dateKey)->name : null;
            if ($isHoliday) {
                $estimated = 0;
            }

            $days[] = [
                'date' => $date,
                'entries' => $dayEntries,
                'total' => $dayTotal,
                'gross_total' => $grossTotal,
                'estimated' => $estimated,
                'is_holiday' => $isHoliday,
                'holiday_name' => $holidayName,
            ];
            $weeklyTotal += $dayTotal;
            $weeklyEstimated += $estimated;
        }

        $openEntry = WorkHour::where('user_id', $employeeId)
            ->whereNull('end_time')
            ->first();

        $data['days'] = $days;
        $data['weekly_total'] = $weeklyTotal;
        $data['weekly_estimated'] = $weeklyEstimated;
        $data['start_of_week'] = $startOfWeek;
        $data['end_of_week'] = $endOfWeek;
        $data['week_offset'] = $weekOffset;
        $data['open_entry'] = $openEntry;
        $data['employee_id'] = $employeeId;

        return view('rrhh.time_entry.index', $data);
    }
}
