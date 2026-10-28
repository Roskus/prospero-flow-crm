<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Every branch gets a cash account in its company currency, named with the application locale.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('branch')
            ->join('company', 'company.id', '=', 'branch.company_id')
            ->whereNotExists(fn ($query) => $query->select(DB::raw(1))
                ->from('bank_account')
                ->whereColumn('bank_account.branch_id', 'branch.id')
                ->where('bank_account.type', 'cash'))
            ->select([
                'branch.id',
                'branch.company_id',
                'branch.name',
                'branch.country_id as branch_country_id',
                'company.country_id as company_country_id',
                'company.currency',
            ])
            ->orderBy('branch.id')
            ->chunk(500, function ($branches): void {
                $now = now();

                DB::table('bank_account')->insert($branches->map(fn (object $branch): array => [
                    'company_id' => $branch->company_id,
                    'branch_id' => $branch->id,
                    'type' => 'cash',
                    'account_name' => mb_substr(__('Cash').' '.$branch->name, 0, 80),
                    'country_id' => strtolower((string) ($branch->company_country_id ?: $branch->branch_country_id ?: 'es')),
                    'currency' => strtoupper((string) $branch->currency),
                    'opening_balance' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all());
            });
    }

    public function down(): void
    {
        DB::table('bank_account')
            ->where('type', 'cash')
            ->whereNotNull('branch_id')
            ->whereNotExists(fn ($query) => $query->select(DB::raw(1))
                ->from('transaction')
                ->whereColumn('transaction.bank_account_id', 'bank_account.id'))
            ->delete();
    }
};
