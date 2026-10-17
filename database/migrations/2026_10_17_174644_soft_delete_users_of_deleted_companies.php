<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Soft delete the active users of companies that are already soft deleted,
     * using the company deletion date so a later restore brings them back.
     */
    public function up(): void
    {
        $deletedCompanies = DB::table('company')
            ->whereNotNull('deleted_at')
            ->pluck('deleted_at', 'id');

        foreach ($deletedCompanies as $companyId => $deletedAt) {
            DB::table('user')
                ->where('company_id', $companyId)
                ->whereNull('deleted_at')
                ->update(['deleted_at' => $deletedAt]);
        }
    }

    /**
     * Restore the users deleted by this migration.
     */
    public function down(): void
    {
        $deletedCompanies = DB::table('company')
            ->whereNotNull('deleted_at')
            ->pluck('deleted_at', 'id');

        foreach ($deletedCompanies as $companyId => $deletedAt) {
            DB::table('user')
                ->where('company_id', $companyId)
                ->where('deleted_at', $deletedAt)
                ->update(['deleted_at' => null]);
        }
    }
};
