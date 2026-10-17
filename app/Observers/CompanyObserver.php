<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Company;
use App\Models\User;

class CompanyObserver
{
    /**
     * Soft delete the company users with the same timestamp, so they can't log in.
     */
    public function deleted(Company $company): void
    {
        if ($company->isForceDeleting()) {
            return;
        }

        User::query()
            ->where('company_id', $company->id)
            ->update(['deleted_at' => $company->deleted_at]);
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
}
