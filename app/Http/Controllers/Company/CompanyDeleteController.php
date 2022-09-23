<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyDeleteController extends Controller
{
    public function delete(Request $request, int $id)
    {
        $company = Company::find($id);
        $company->delete();
        $company->save();

        return redirect('/company');
    }
}
