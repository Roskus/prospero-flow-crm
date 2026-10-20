<?php

declare(strict_types=1);

namespace App\Http\Controllers\Company;

use App\Http\Controllers\MainController;
use App\Http\Requests\CompanyUpdateRequest;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Squire\Models\Country;
use Squire\Models\Currency;

class CompanyUpdateController extends MainController
{
    public function update(CompanyUpdateRequest $request, int $id)
    {
        $company = Company::withTrashed()->findOrFail($id);
        $data['company'] = $company;
        $data['countries'] = Country::all();
        $data['currencies'] = Currency::all();
        $data['parentCompanies'] = Auth::user()->hasRole('SuperAdmin') ? $company->getParentCandidates($company->id) : collect();

        return view('company/company', $data);
    }
}
