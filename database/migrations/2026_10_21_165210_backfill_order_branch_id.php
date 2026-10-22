<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Existing orders belong to the main branch of their company.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('
            UPDATE `order` o
            JOIN branch b ON b.company_id = o.company_id AND b.is_main = 1
            SET o.branch_id = b.id
            WHERE o.branch_id IS NULL
        ');
    }

    public function down(): void
    {
        DB::table('order')->update(['branch_id' => null]);
    }
};
