<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('time_off', function (Blueprint $table) {
            $table->string('attachment', 255)->nullable()->after('reason');
        });
    }

    public function down(): void
    {
        Schema::table('time_off', function (Blueprint $table) {
            $table->dropColumn('attachment');
        });
    }
};
