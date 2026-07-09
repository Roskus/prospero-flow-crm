<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('time_off', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('user_id');
            $table->string('type', 20); // vacation, sick, personal
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('days_used', 5, 1);
            $table->text('reason')->nullable();
            $table->string('status', 20)->default('pending'); // pending, approved, rejected
            $table->unsignedBigInteger('manager_approved_by')->nullable();
            $table->dateTime('manager_approved_at')->nullable();
            $table->unsignedBigInteger('rrhh_approved_by')->nullable();
            $table->dateTime('rrhh_approved_at')->nullable();
            $table->text('rejected_reason')->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('company')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('user')->cascadeOnDelete();
            $table->foreign('manager_approved_by')->references('id')->on('user')->nullOnDelete();
            $table->foreign('rrhh_approved_by')->references('id')->on('user')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_off');
    }
};
