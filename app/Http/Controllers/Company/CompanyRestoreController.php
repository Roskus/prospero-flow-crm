<?php

declare(strict_types=1);

namespace App\Http\Controllers\Company;

use App\Http\Controllers\MainController;
use App\Http\Requests\CompanyRestoreRequest;
use App\Models\Company;

class CompanyRestoreController extends MainController
{
    public function restore(CompanyRestoreRequest $request, int $id)
    {
        $company = Company::onlyTrashed()->find($id);

        if (! $company) {
            return redirect(route('company.index'))->with('error', __('Company not found'));
        }

        $company->restore();
        $company->status = Company::ACTIVE;
        $company->save();

        return redirect(route('company.index'))->with('success', __('Company restored successfully'));
    }
}
