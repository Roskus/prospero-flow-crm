<?php

declare(strict_types=1);

namespace App\Http\Controllers\Company;

use App\Http\Controllers\MainController;
use App\Models\Company;
use App\Services\SecurityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class CompanySwitchController extends MainController
{
    public function __construct(private SecurityLogger $securityLogger) {}

    /**
     * Change the active company; every existing company filter follows user.company_id.
     */
    public function switch(int $id): RedirectResponse
    {
        $user = Auth::user();
        $company = Company::query()->find($id);

        if ($company === null || ! $user->canAccessCompany($company->id)) {
            $this->securityLogger->log('company_switch_denied', ['to_company_id' => $id]);

            abort(403);
        }

        $fromCompanyId = (int) $user->company_id;
        $user->company_id = $company->id;
        $user->save();

        $this->securityLogger->log('company_switched', [
            'from_company_id' => $fromCompanyId,
            'to_company_id' => $company->id,
        ]);

        return redirect('/');
    }
}
