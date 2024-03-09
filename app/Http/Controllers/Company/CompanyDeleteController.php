<?php

declare(strict_types=1);

namespace App\Http\Controllers\Company;

use App\Http\Controllers\MainController;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyDeleteController extends MainController
{
    public function delete(Request $request, int $id)
    {
        $company = Company::find($id);
        if ((int) Auth::user()->company_id !== Company::DEFAULT_COMPANY) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $company->delete();
        $company->save();

        return redirect('/company');
    }
}
