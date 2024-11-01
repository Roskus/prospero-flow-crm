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
        $company = new Company;
        if (Auth::user()->hasRole('SuperAdmin')) {
            $companies = $company->getAllPaginated();
        } else {
            $companies = Company::where('id', (int) Auth::user()->company_id)->paginate();
        }
        $data['companies'] = $companies;

        return view('company.index', $data);
    }
}
