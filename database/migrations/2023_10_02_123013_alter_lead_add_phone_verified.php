<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lead', function (Blueprint $table) {
            $table->boolean('phone_verified')->nullable()->after('phone')->default(false);
            $table->boolean('phone2_verified')->nullable()->after('phone2')->default(false);
            $table->boolean('mobile_verified')->nullable()->after('mobile')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lead', function (Blueprint $table) {
            $table->dropColumn('phone_verified');
            $table->dropColumn('phone2_verified');
            $table->dropColumn('mobile_verified');
        });
    }
};
