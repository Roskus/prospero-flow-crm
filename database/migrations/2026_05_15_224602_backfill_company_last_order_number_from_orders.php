<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('
            UPDATE company c
            SET c.last_order_number = (
                SELECT COALESCE(MAX(o.order_number), 0)
                FROM `order` o
                WHERE o.company_id = c.id
                  AND o.deleted_at IS NULL
            )
        ');
    }

    public function down(): void
    {
        DB::table('company')->update(['last_order_number' => 0]);
    }
};
