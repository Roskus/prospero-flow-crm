<?php

declare(strict_types=1);

namespace App\Http\Controllers\Rrhh\Employee;

use App\Http\Controllers\MainController;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeDeleteController extends MainController
{
    public function delete(Request $request, int $id): RedirectResponse
    {
        $employee = User::where('company_id', Auth::user()->company_id)
            ->where('id', $id)
            ->where('is_employee', true)
            ->firstOrFail();
        $employee->delete();

        return redirect('/rrhh')->with(['status' => 'success', 'message' => __('Employee deleted successfully')]);
    }
}
