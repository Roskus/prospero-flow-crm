<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Clocking in creates the entry without an end time, which a NOT NULL column refuses.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_hour', function (Blueprint $table) {
            $table->dateTime('end_time')->nullable()->change();
        });
    }

    /**
     * Open entries have no end time to keep, so they are closed at their start time before restoring NOT NULL.
     */
    public function down(): void
    {
        DB::table('work_hour')->whereNull('end_time')->update(['end_time' => DB::raw('start_time')]);

        Schema::table('work_hour', function (Blueprint $table) {
            $table->dateTime('end_time')->nullable(false)->change();
        });
    }
};
