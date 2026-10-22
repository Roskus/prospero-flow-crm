<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Every existing user gets the main branch of their default company (or of their company).
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('
            UPDATE `user` u
            JOIN branch b ON b.company_id = COALESCE(u.default_company_id, u.company_id) AND b.is_main = 1
            SET u.default_branch_id = b.id
            WHERE u.default_branch_id IS NULL
        ');
    }

    public function down(): void
    {
        DB::table('user')->update(['default_branch_id' => null]);
    }
};
