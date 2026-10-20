<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\MainController;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\CompanyAccessService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserSaveController extends MainController
{
    public function save(UserRequest $request, CompanyAccessService $companyAccessService)
    {
        if (empty($request->id)) {
            $user = new User;
            $user->created_at = now();
            // Company should be assigned on create
            $user->company_id = Auth::user()->company_id;
        } else {
            $user = User::query()
                ->manageableBy(Auth::user())
                ->findOrFail($request->id);
        }

        if (Auth::user()->hasRole('SuperAdmin')) {
            $this->setCompany($user, (int) $request->company_id);
        }

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->lang = $request->lang;
        $user->employee_number = $request->employee_number;
        $user->hire_date = $request->hire_date;
        $user->is_employee = $request->boolean('is_employee', true);
        $user->manager_id = $request->manager_id;
        if ($request->has('iban') && Auth::user()->canSeeBankDetails()) {
            $user->iban = $request->iban;
        }
        $user->vacation_days_override = $request->vacation_days_override;
        $user->weekly_hours_override = $request->weekly_hours_override;
        if ($request->timezone) {
            $user->timezone = $request->timezone;
        }

        if (
            ! empty($request->password) &&
            ! empty($request->password_confirmation) &&
            ($request->password == $request->password_confirmation)
        ) {
            $user->password = Hash::make($request->password);
        }

        if (! empty($request->roles) && Auth::user()->canChangeRolesOf($user)) {
            $user->syncRoles(Auth::user()->rolesToSync($user, $request->roles));
        }

        $user->updated_at = now();
        $user->save();

        if ($user->wasChanged('default_company_id')) {
            $companyAccessService->forgetCompanies($user);
        }

        if (Auth::user()->hasRole('SuperAdmin') && $request->has('extra_companies')) {
            $companyAccessService->syncExtraCompanies($user, $request->input('extra_companies', []), Auth::user());
        }

        return redirect('/user');
    }

    /**
     * For multi-company users the form edits their default company and leaves the active one untouched,
     * unless the default company changes.
     */
    private function setCompany(User $user, int $companyId): void
    {
        if (! $user->hasMultiCompanyAccess()) {
            $user->company_id = $companyId;

            return;
        }

        if ($companyId !== $user->defaultCompanyId()) {
            $user->default_company_id = $companyId;
            $user->company_id = $companyId;
        }
    }
}
