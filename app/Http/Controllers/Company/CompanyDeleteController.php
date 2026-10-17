<?php

declare(strict_types=1);

namespace App\Http\Controllers\Company;

use App\Http\Controllers\MainController;
use App\Http\Requests\CompanyDeleteRequest;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;

class CompanyDeleteController extends MainController
{
    public function delete(CompanyDeleteRequest $request, int $id)
    {
        $company = Company::find($id);

        if (! $company) {
            return redirect(route('company.index'))->with('error', __('Company not found'));
        }

        if ((int) $company->id === (int) Auth::user()->company_id) {
            return redirect(route('company.index'))->with('error', __('You cannot delete your own company'));
        }

        $company->status = Company::INACTIVE;
        $company->save();
        $company->delete();

        return redirect(route('company.index'))->with('success', __('Company deleted successfully'));
    }
}
