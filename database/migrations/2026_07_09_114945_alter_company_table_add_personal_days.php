<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('company')->where('vacation_days_per_year', 22)->update(['vacation_days_per_year' => 20]);

        Schema::table('company', function (Blueprint $table) {
            $table->integer('personal_days_per_year')->default(2)->after('weekly_hours_full_time');
        });
    }

    public function down(): void
    {
        Schema::table('company', function (Blueprint $table) {
            $table->dropColumn('personal_days_per_year');
        });

        DB::table('company')->where('vacation_days_per_year', 20)->update(['vacation_days_per_year' => 22]);
    }
};
