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
        Schema::table('email', function (Blueprint $table) {
            $table->string('uuid', 36)->nullable()->unique()->after('lang');
            $table->timestamp('opened_at')->nullable()->after('uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email', function (Blueprint $table) {
            $table->dropColumn(['uuid', 'opened_at']);
        });
    }
};
