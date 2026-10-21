<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Columns are listed explicitly instead of using the `address()` macro, so this migration does not change if the macro does.
     */
    public function up(): void
    {
        foreach (['contact', 'supplier_contact'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->renameColumn('country', 'country_id');
            });

            Schema::table($tableName, function (Blueprint $table) {
                $table->string('province', 80)->nullable()->after('country_id');
                $table->string('city', 50)->nullable()->after('province');
                $table->string('locality', 80)->nullable()->after('city');
                $table->string('street', 80)->nullable()->after('locality');
                $table->string('address_extra', 80)->nullable()->after('street');
                $table->string('zipcode', 10)->nullable()->after('address_extra');
                $table->decimal('latitude', 10, 8)->nullable()->after('zipcode');
                $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            });
        }
    }

    public function down(): void
    {
        foreach (['contact', 'supplier_contact'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn(['province', 'city', 'locality', 'street', 'address_extra', 'zipcode', 'latitude', 'longitude']);
            });

            Schema::table($tableName, function (Blueprint $table) {
                $table->renameColumn('country_id', 'country');
            });
        }
    }
};
