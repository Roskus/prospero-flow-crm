<?php

declare(strict_types=1);

namespace App\Http\Controllers\Rrhh\Employee;

use App\Http\Controllers\MainController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeSaveController extends MainController
{
    public function save(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:user,email',
            'phone' => 'nullable|string|max:20',
            'employee_number' => 'nullable|string|max:50|unique:user,employee_number',
            'is_employee' => 'boolean',
            'manager_id' => 'nullable|exists:user,id',
            'vacation_days_override' => 'nullable|integer|min:0',
            'weekly_hours_override' => 'nullable|numeric|min:0|max:168',
        ]);

        $validated['company_id'] = Auth::user()->company_id;
        $validated['password'] = bcrypt($request->input('password', 'changeme'));

        User::create($validated);

        return redirect('/rrhh')->with(['status' => 'success', 'message' => __('Employee created successfully')]);
    }
}
