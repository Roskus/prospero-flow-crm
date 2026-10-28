<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\User;
use App\Services\WorkHourService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;

class ReportHoursRequest extends FormRequest
{
    public const int MAX_DAYS = 366;

    public function authorize(): bool
    {
        $user = Auth::user();

        return $user !== null && ($user->can('read hours report') || $user->can('manage own timesheet'));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'month' => ['nullable', 'date_format:Y-m'],
            'from' => ['nullable', 'required_with:to', 'date_format:Y-m-d'],
            'to' => ['nullable', 'required_with:from', 'date_format:Y-m-d', 'after_or_equal:from'],
            'employee_id' => ['nullable', 'integer'],
        ];
    }

    /**
     * @return list<callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty() || ! $this->filled('from')) {
                    return;
                }

                if (Carbon::parse($this->input('from'))->diffInDays(Carbon::parse($this->input('to'))) + 1 > self::MAX_DAYS) {
                    $validator->errors()->add('to', __('The period cannot be longer than :days days.', ['days' => self::MAX_DAYS]));
                }
            },
        ];
    }

    public function canSeeEveryEmployee(): bool
    {
        return Auth::user()->can('read hours report');
    }

    public function periodFrom(): Carbon
    {
        return $this->filled('from')
            ? Carbon::parse($this->validated('from'))->startOfDay()
            : $this->month()->startOfMonth();
    }

    public function periodTo(): Carbon
    {
        return $this->filled('to')
            ? Carbon::parse($this->validated('to'))->startOfDay()
            : $this->month()->endOfMonth()->startOfDay();
    }

    /**
     * Employees of the active company in the report: every employee, or one, with `read hours report`;
     * otherwise only the user.
     *
     * @return Collection<int, User>
     */
    public function employees(): Collection
    {
        $user = Auth::user();

        if (! $this->canSeeEveryEmployee()) {
            return User::whereKey($user->id)->get();
        }

        $query = User::where('company_id', $user->company_id)
            ->where('is_employee', true)
            ->orderBy('first_name')
            ->orderBy('last_name');

        if ($this->filled('employee_id')) {
            $query->whereKey($this->integer('employee_id'));
        }

        return $query->get();
    }

    /**
     * @return array{rows: list<array{employee: User, worked: float, expected: float, difference: float, days_worked: int, open_entries: int}>, totals: array{worked: float, expected: float, difference: float, days_worked: int, open_entries: int}, from: Carbon, to: Carbon}
     */
    public function reportData(WorkHourService $workHourService): array
    {
        $from = $this->periodFrom();
        $to = $this->periodTo();
        $rows = $workHourService->summary($this->employees(), $from, $to);

        return [
            'rows' => $rows,
            'totals' => [
                'worked' => round(array_sum(array_column($rows, 'worked')), 2),
                'expected' => round(array_sum(array_column($rows, 'expected')), 2),
                'difference' => round(array_sum(array_column($rows, 'difference')), 2),
                'days_worked' => array_sum(array_column($rows, 'days_worked')),
                'open_entries' => array_sum(array_column($rows, 'open_entries')),
            ],
            'from' => $from,
            'to' => $to,
        ];
    }

    private function month(): Carbon
    {
        return $this->filled('month') ? Carbon::createFromFormat('Y-m-d', $this->validated('month').'-01') : Carbon::now();
    }
}
