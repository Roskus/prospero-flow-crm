<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Bank\Account;
use App\Models\Branch;
use App\Models\Company;

class CashAccountService
{
    /**
     * Returns the cash account of the branch, creating it in the company currency when it has none.
     */
    public function createForBranch(Branch $branch): Account
    {
        $existing = Account::query()
            ->where('branch_id', $branch->id)
            ->where('type', Account::TYPE_CASH)
            ->first();

        if ($existing !== null) {
            return $existing;
        }

        $company = Company::withTrashed()->findOrFail($branch->company_id);

        $account = new Account([
            'company_id' => $company->id,
            'type' => Account::TYPE_CASH,
            'account_name' => mb_substr(__('Cash').' '.$branch->name, 0, 80),
            'country_id' => strtolower((string) ($company->country_id ?: $branch->getAttribute('country_id') ?: 'es')),
            'currency' => strtoupper((string) $company->currency),
        ]);
        $account->branch_id = $branch->id;
        $account->save();

        return $account;
    }
}
