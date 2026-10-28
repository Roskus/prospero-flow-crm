<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Transactions had no currency: they take the currency of their account, or the company currency without one.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('transaction')
            ->join('bank_account', 'bank_account.id', '=', 'transaction.bank_account_id')
            ->whereNull('transaction.currency')
            ->whereNotNull('bank_account.currency')
            ->update(['transaction.currency' => DB::raw('UPPER(bank_account.currency)')]);

        DB::table('transaction')
            ->join('company', 'company.id', '=', 'transaction.company_id')
            ->whereNull('transaction.currency')
            ->update(['transaction.currency' => DB::raw('UPPER(company.currency)')]);
    }

    /**
     * The currency column is dropped by the previous migration on rollback, so there is nothing to undo here.
     */
    public function down(): void {}
};
