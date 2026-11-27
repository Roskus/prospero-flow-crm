<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const array TABLES = ['lead', 'customer', 'contact', 'supplier_contact'];

    /**
     * email_verified holds three states (App\Enums\EmailVerificationStatus: 0 pending, 1 valid,
     * 3 invalid), so it is no longer declared as a boolean. Stored values do not change.
     */
    public function up(): void
    {
        foreach (self::TABLES as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->unsignedTinyInteger('email_verified')->default(0)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (self::TABLES as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->boolean('email_verified')->default(false)->change();
            });
        }
    }
};
