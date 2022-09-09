<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;
use App\Models\Company;

class CompanyDeleteController
{
    public function delete(Request $request, int $id)
    {
        $company = Company::find($id);
        $company->delete();
        $company->save();
        return redirect('/company');
    }
}
