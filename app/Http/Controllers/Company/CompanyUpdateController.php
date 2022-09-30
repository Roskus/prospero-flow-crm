<?php
declare(strict_types=1);
namespace App\Http\Controllers\Company;

use App\Http\Controllers\MainController;
use App\Models\Company;
use Illuminate\Http\Request;
use Squire\Models\Country;

class CompanyUpdateController extends MainController
{
    public function update(Request $request, int $id)
    {
        $company = Company::find($id);
        $data['company'] = $company;
        $data['countries'] = Country::all();

        return view('company/company', $data);
    }
}
