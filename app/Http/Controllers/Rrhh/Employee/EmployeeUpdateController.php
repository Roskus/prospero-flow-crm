<?php

declare(strict_types=1);

namespace App\Http\Controllers\Rrhh\Employee;

use App\Http\Controllers\MainController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeUpdateController extends MainController
{
    public function update(Request $request, int $id)
    {
        $employee = User::where('company_id', Auth::user()->company_id)
            ->where('id', $id)
            ->where('is_employee', true)
            ->firstOrFail();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:user,email,'.$id,
            'phone' => 'nullable|string|max:20',
            'employee_number' => 'nullable|string|max:50|unique:user,employee_number,'.$id,
            'is_employee' => 'boolean',
            'manager_id' => 'nullable|exists:user,id',
            'vacation_days_override' => 'nullable|integer|min:0',
            'weekly_hours_override' => 'nullable|numeric|min:0|max:168',
        ]);

        $employee->update($validated);

        return redirect('/rrhh')->with(['status' => 'success', 'message' => __('Employee updated successfully')]);
    }
}
