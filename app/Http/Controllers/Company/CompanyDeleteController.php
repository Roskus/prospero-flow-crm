<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\MainController;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyDeleteController extends MainController
{
    public function delete(Request $request, int $id)
    {
        $company = Company::find($id);
        $company->delete();
        $company->save();

        return redirect('/company');
    }
}
