<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payroll', function (Blueprint $table) {
            $table->decimal('gross_amount', 10, 2)->nullable()->after('amount');
            $table->renameColumn('amount', 'net_amount');
            $table->unsignedSmallInteger('period_year')->nullable()->after('net_amount');
            $table->unsignedTinyInteger('period_month')->nullable()->after('period_year');
        });
    }

    public function down(): void
    {
        Schema::table('payroll', function (Blueprint $table) {
            $table->renameColumn('net_amount', 'amount');
            $table->dropColumn(['gross_amount', 'period_year', 'period_month']);
        });
    }
};
