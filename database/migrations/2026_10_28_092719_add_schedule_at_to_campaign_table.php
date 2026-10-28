<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The scheduled date and time are entered in the user's time zone; schedule_at keeps the same moment in UTC for the sender.
 * Existing campaigns were compared in the application time zone, so they keep that moment.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaign', function (Blueprint $table) {
            $table->dateTime('schedule_at')->nullable()->after('schedule_send_time');
            $table->index(['send_at', 'schedule_at']);
        });

        DB::table('campaign')
            ->whereNotNull('schedule_send_date')
            ->update(['schedule_at' => DB::raw("CONCAT(schedule_send_date, ' ', COALESCE(schedule_send_time, '00:00:00'))")]);
    }

    public function down(): void
    {
        Schema::table('campaign', function (Blueprint $table) {
            $table->dropIndex(['send_at', 'schedule_at']);
            $table->dropColumn('schedule_at');
        });
    }
};
