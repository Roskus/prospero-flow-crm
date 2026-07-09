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
        Schema::table('user', function (Blueprint $table) {
            $table->string('employee_number', 50)->nullable()->after('signature_html');
            $table->boolean('is_employee')->default(true)->after('employee_number');
            $table->date('hire_date')->nullable()->after('is_employee');
            $table->integer('vacation_days_override')->nullable()->after('hire_date');
            $table->decimal('weekly_hours_override', 5, 2)->nullable()->after('vacation_days_override');
            $table->unique(['company_id', 'employee_number']);
        });
    }

    public function down(): void
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropColumn(['employee_number', 'is_employee', 'vacation_days_override', 'weekly_hours_override', 'hire_date']);
        });
    }
};
