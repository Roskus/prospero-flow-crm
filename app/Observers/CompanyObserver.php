<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Company;
use App\Models\User;
use App\Services\CompanyAccessService;
use Illuminate\Support\Facades\DB;

class CompanyObserver
{
    public function __construct(private CompanyAccessService $companyAccessService) {}

    /**
     * The company switcher caches company names.
     */
    public function updated(Company $company): void
    {
        if ($company->wasChanged('name')) {
            $this->companyAccessService->forgetCompanyUsers($company);
        }
    }

    /**
     * Soft delete the company users with the same timestamp, so they can't log in.
     */
    public function deleted(Company $company): void
    {
        if ($company->isForceDeleting()) {
            return;
        }

        $this->moveMultiCompanyUsersToTheirDefaultCompany($company);

        User::query()
            ->where('company_id', $company->id)
            ->update(['deleted_at' => $company->deleted_at]);

        $this->companyAccessService->forgetCompanyUsers($company);
    }

    /**
     * Restore only the users that were deleted together with the company.
     */
    public function restoring(Company $company): void
    {
        $deletedAt = $company->getOriginal('deleted_at');

        if ($deletedAt === null) {
            return;
        }

        User::onlyTrashed()
            ->where('company_id', $company->id)
            ->where('deleted_at', $deletedAt)
            ->update(['deleted_at' => null]);
    }

    public function restored(Company $company): void
    {
        $this->companyAccessService->forgetCompanyUsers($company);
    }

    /**
     * Multi-company users working in the deleted company go back to their default company, so they are
     * not deleted with a client company; the ones whose default company it is are deleted (and restored) with it.
     */
    private function moveMultiCompanyUsersToTheirDefaultCompany(Company $company): void
    {
        User::query()
            ->where('company_id', $company->id)
            ->whereNotNull('default_company_id')
            ->update(['company_id' => DB::raw('default_company_id')]);

        User::query()
            ->where('default_company_id', $company->id)
            ->update(['company_id' => $company->id]);
    }
}
