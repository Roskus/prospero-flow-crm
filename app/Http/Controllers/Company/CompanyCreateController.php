<?php

declare(strict_types=1);

namespace App\Http\Controllers\Company;

use App\Http\Controllers\MainController;
use App\Http\Requests\CompanyCreateRequest;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Squire\Models\Country;
use Squire\Models\Currency;

class CompanyCreateController extends MainController
{
    public function create(CompanyCreateRequest $request)
    {
        $company = new Company;
        $data['company'] = $company;
        $data['countries'] = Country::all();
        $data['currencies'] = Currency::all();
        $data['parentCompanies'] = Auth::user()->hasRole('SuperAdmin') ? $company->getParentCandidates(null) : collect();

        return view('company.company', $data);
    }
}
