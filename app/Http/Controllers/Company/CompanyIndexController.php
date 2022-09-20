<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyIndexController extends Controller
{
    public function index(Request $request)
    {
        $company = new Company();
        $data['companies'] = $company->getAll();

        return view('company.index', $data);
    }
}
