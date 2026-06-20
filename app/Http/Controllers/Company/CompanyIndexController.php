<?php

declare(strict_types=1);

namespace App\Http\Controllers\Company;

use App\Http\Controllers\MainController;
use App\Http\Requests\CompanyIndexRequest;
use App\Models\Company;

class CompanyIndexController extends MainController
{
    public function index(CompanyIndexRequest $request)
    {
        $company = new Company;
        $companies = $company->getAllPaginated();
        $data['companies'] = $companies;

        return view('company.index', $data);
    }
}
