<?php

declare(strict_types=1);

namespace App\Http\Controllers\Company;

use App\Http\Controllers\MainController;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyIndexController extends MainController
{
    public function index(Request $request)
    {
        $company = new Company();
        $data['companies'] = $company->getAllPaginated();

        return view('company.index', $data);
    }
}
