<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;

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
