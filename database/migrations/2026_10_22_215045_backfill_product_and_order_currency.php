<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Products and orders were never given a currency from the UI, so they kept the column default EUR.
 * Rows still at EUR in companies with another currency get the company currency.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['product', 'order'] as $table) {
            DB::table($table)
                ->join('company', 'company.id', '=', "{$table}.company_id")
                ->where("{$table}.currency", 'EUR')
                ->where('company.currency', '!=', 'EUR')
                ->update(["{$table}.currency" => DB::raw('company.currency')]);
        }
    }

    /**
     * The previous EUR default carried no information, so there is nothing to restore.
     */
    public function down(): void {}
};
