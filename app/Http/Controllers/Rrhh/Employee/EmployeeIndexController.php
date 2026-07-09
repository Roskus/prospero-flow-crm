<?php

declare(strict_types=1);

namespace App\Http\Controllers\Rrhh\Employee;

use App\Http\Controllers\MainController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeIndexController extends MainController
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = User::where('company_id', Auth::user()->company_id)
            ->where('is_employee', true);

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                    ->orWhere('last_name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('employee_number', 'LIKE', "%{$search}%");
            });
        }

        $data['employees'] = $query->with('manager')->orderBy('first_name')->paginate(20);

        return view('rrhh.employee.index', $data);
    }
}
