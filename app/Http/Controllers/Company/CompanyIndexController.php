<?php

declare(strict_types=1);

namespace App\Http\Controllers\Company;

use App\Http\Controllers\MainController;
use App\Http\Requests\CompanyIndexRequest;
use App\Models\Company;
use App\Models\User;

class CompanyIndexController extends MainController
{
    public function index(CompanyIndexRequest $request)
    {
        $company = new Company;
        $companies = $company->getAllPaginated(filter: $request->validated('filter'), user: $request->user());
        $data['companies'] = $companies;
        $data['availableCompaniesQuota'] = $request->user()->hasRole(User::MULTI_COMPANY_ADMIN) && ! $request->user()->hasRole('SuperAdmin')
            ? Company::query()->find($request->user()->defaultCompanyId())?->availableClientQuota()
            : null;

        return view('company.index', $data);
    }
}
