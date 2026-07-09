<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company', function (Blueprint $table) {
            $table->integer('vacation_days_per_year')->default(22)->after('last_order_number');
            $table->decimal('weekly_hours_full_time', 5, 2)->default(37.00)->after('vacation_days_per_year');
        });
    }

    public function down(): void
    {
        Schema::table('company', function (Blueprint $table) {
            $table->dropColumn(['vacation_days_per_year', 'weekly_hours_full_time']);
        });
    }
};
