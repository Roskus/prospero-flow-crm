<?php

declare(strict_types=1);

namespace App\Http\Controllers\Report;

use App\Http\Controllers\MainController;
use App\Http\Requests\ReportHoursRequest;
use App\Models\User;
use App\Services\WorkHourService;
use Illuminate\Contracts\View\View;

class ReportHoursController extends MainController
{
    public function index(ReportHoursRequest $request, WorkHourService $workHourService): View
    {
        $user = $request->user();

        return view('report.hours', [
            ...$request->reportData($workHourService),
            'month' => $request->filled('from') ? null : $request->periodFrom()->format('Y-m'),
            'employee_id' => $request->canSeeEveryEmployee() ? ($request->integer('employee_id') ?: null) : null,
            'can_see_every_employee' => $request->canSeeEveryEmployee(),
            'employees' => $request->canSeeEveryEmployee()
                ? User::query()->where('company_id', $user->company_id)->where('is_employee', true)->orderBy('first_name')->orderBy('last_name')->get()
                : collect(),
        ]);
    }
}
