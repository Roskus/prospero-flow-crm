<?php

declare(strict_types=1);

namespace App\Http\Controllers\Company;

use App\Http\Controllers\MainController;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyIndexController extends MainController
{
    public function index(Request $request)
    {
        $company = new Company();
        $data['companies'] = Auth::user()->hasRole('SuperAdmin') ? $company->getAllPaginated() : Company::where('id', Auth::user()->company_id)->paginate();

        return view('company.index', $data);
    }
}
