<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Inactive users keep their data but cannot log in (web or API).
     */
    public function up(): void
    {
        Schema::table('user', function (Blueprint $table) {
            $table->boolean('active')->default(true)->after('must_change_password');
        });
    }

    public function down(): void
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropColumn('active');
        });
    }
};
