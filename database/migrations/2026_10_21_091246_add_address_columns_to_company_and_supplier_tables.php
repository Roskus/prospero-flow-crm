<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Columns are listed explicitly instead of using the `address()` macro, so this migration does not change if the macro does.
     */
    public function up(): void
    {
        foreach (['company', 'supplier'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->string('province', 80)->nullable()->change();
                $table->string('locality', 80)->nullable()->after('city');
                $table->string('address_extra', 80)->nullable()->after('street');
                $table->decimal('latitude', 10, 8)->nullable()->after('zipcode');
                $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            });
        }
    }

    public function down(): void
    {
        foreach (['company', 'supplier'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn(['locality', 'address_extra', 'latitude', 'longitude']);
            });

            if (! DB::table($tableName)->whereRaw('CHAR_LENGTH(province) > 50')->exists()) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->string('province', 50)->nullable()->change();
                });
            }
        }
    }
};
