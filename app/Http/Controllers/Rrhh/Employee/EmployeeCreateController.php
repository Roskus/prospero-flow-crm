<?php

declare(strict_types=1);

namespace App\Http\Controllers\Rrhh\Employee;

use App\Http\Controllers\MainController;

class EmployeeCreateController extends MainController
{
    public function create()
    {
        return view('rrhh.employee.create');
    }
}
