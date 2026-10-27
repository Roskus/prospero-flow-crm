<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Orders get their own date so historical orders can be loaded; existing orders take the date they were created.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order', function (Blueprint $table) {
            $table->date('order_date')->nullable()->after('status');
        });

        DB::table('order')->whereNull('order_date')->update(['order_date' => DB::raw('CAST(created_at AS DATE)')]);
        DB::table('order')->whereNull('order_date')->update(['order_date' => now()->toDateString()]);

        Schema::table('order', function (Blueprint $table) {
            $table->date('order_date')->nullable(false)->default(DB::raw('(CURRENT_DATE)'))->change();
            $table->index(['company_id', 'order_date']);
        });
    }

    public function down(): void
    {
        Schema::table('order', function (Blueprint $table) {
            $table->dropIndex(['company_id', 'order_date']);
            $table->dropColumn('order_date');
        });
    }
};
