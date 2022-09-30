<?php
declare(strict_types=1);

namespace App\Http\Controllers\Company;

use App\Http\Controllers\MainController;
use App\Models\Company;
use Illuminate\Http\Request;
use Squire\Models\Country;

class CompanyCreateController extends MainController
{
    public function create(Request $request)
    {
        $company = new Company();
        $data['company'] = $company;
        $data['countries'] = Country::all();

        return view('company.company', $data);
    }
}
