<?php

declare(strict_types=1);

namespace App\Http\Controllers\Company;

use App\Http\Controllers\MainController;
use App\Http\Requests\CompanyDeleteRequest;
use App\Models\Company;

class CompanyDeleteController extends MainController
{
    public function delete(CompanyDeleteRequest $request, int $id)
    {
        $company = Company::find($id);

        if (! $company) {
            return redirect(route('company.index'))->with('error', __('Company not found'));
        }

        $company->status = Company::INACTIVE;
        $company->save();
        $company->delete();

        return redirect(route('company.index'))->with('success', __('Company deleted successfully'));
    }
}
