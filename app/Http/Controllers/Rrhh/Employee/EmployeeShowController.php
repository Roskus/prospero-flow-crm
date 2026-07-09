<?php

declare(strict_types=1);

namespace App\Http\Controllers\Rrhh\Employee;

use App\Http\Controllers\MainController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeShowController extends MainController
{
    public function show(Request $request, int $id)
    {
        $employee = User::where('company_id', Auth::user()->company_id)
            ->where('id', $id)
            ->where('is_employee', true)
            ->with('manager')
            ->firstOrFail();

        $data['employee'] = $employee;

        return view('rrhh.employee.show', $data);
    }
}
