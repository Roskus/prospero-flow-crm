<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Branch;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Keeps the "exactly one main branch per company" rule: every write that can change the main branch locks the
 * company row, so creating, deleting and setting the main branch of a company are serialized.
 */
class BranchService
{
    /**
     * Create the main branch from the company data and give it to the company users that have no default branch yet.
     */
    public function createMainBranch(Company $company): Branch
    {
        return DB::transaction(function () use ($company): Branch {
            $this->lockCompany($company->id);

            $branch = Branch::ofCompany($company->id)->where('is_main', true)->first();

            if ($branch === null) {
                $branch = new Branch([
                    'name' => $company->name,
                    'code' => Branch::MAIN_CODE,
                    'is_main' => true,
                    'phone' => $company->phone,
                    'email' => $company->email,
                ]);
                $branch->company_id = $company->id;
                $branch->address = $company->address;
                $branch->save();
            }

            User::withTrashed()
                ->whereNull('default_branch_id')
                ->where(fn ($query) => $query->where('default_company_id', $company->id)
                    ->orWhere(fn ($query) => $query->whereNull('default_company_id')->where('company_id', $company->id)))
                ->update(['default_branch_id' => $branch->id]);

            return $branch;
        });
    }

    /**
     * @return bool false when the branch was deleted before the lock was taken
     */
    public function makeMain(Branch $branch): bool
    {
        return DB::transaction(function () use ($branch): bool {
            $this->lockCompany($branch->company_id);

            $current = Branch::ofCompany($branch->company_id)->find($branch->id);

            if ($current === null) {
                return false;
            }

            Branch::ofCompany($branch->company_id)
                ->where('is_main', true)
                ->whereKeyNot($current->id)
                ->update(['is_main' => false]);

            $current->update(['is_main' => true]);
            $branch->setRawAttributes($current->getAttributes(), true);

            return true;
        });
    }

    /**
     * @return bool false when the branch is the main branch once the lock is taken
     */
    public function delete(Branch $branch): bool
    {
        return DB::transaction(function () use ($branch): bool {
            $this->lockCompany($branch->company_id);

            $current = Branch::ofCompany($branch->company_id)->find($branch->id);

            if ($current === null) {
                return true;
            }

            if ($current->is_main) {
                return false;
            }

            return (bool) $current->delete();
        });
    }

    /**
     * Next free numeric code of the company, padded to 4 digits. Deleted branches count: their code stays taken.
     */
    public function nextCode(Company|int $company): string
    {
        $companyId = $company instanceof Company ? $company->id : $company;

        $max = Branch::withTrashed()
            ->ofCompany($companyId)
            ->pluck('code')
            ->filter(fn (string $code): bool => ctype_digit($code))
            ->map(fn (string $code): int => (int) $code)
            ->max() ?? 0;

        return str_pad((string) ($max + 1), 4, '0', STR_PAD_LEFT);
    }

    private function lockCompany(int $companyId): void
    {
        Company::withTrashed()->whereKey($companyId)->lockForUpdate()->first();
    }
}
