<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Card security codes must not be stored (PCI DSS), so the stored ones are destroyed with the column.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bank_card', function (Blueprint $table) {
            $table->dropColumn('cvv_encrypted');
        });

        DB::table(config('permission.table_names.permissions'))->where('name', 'bank card view cvv')->delete();
        app('cache')->forget(config('permission.cache.key'));
    }

    /**
     * The column comes back empty: the destroyed codes cannot be restored.
     */
    public function down(): void
    {
        Schema::table('bank_card', function (Blueprint $table) {
            $table->text('cvv_encrypted')->nullable()->after('last_four');
        });
    }
};
