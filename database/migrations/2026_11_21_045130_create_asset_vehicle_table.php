<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_vehicle', function (Blueprint $table) {
            $table->foreignId('asset_id')->primary()->constrained('asset')->cascadeOnDelete();
            $table->string('license_plate', 20)->nullable()->index();
            $table->string('vin', 50)->nullable();
            $table->unsignedSmallInteger('manufacturing_year')->nullable();
            $table->string('fuel_type', 20)->nullable();
            $table->unsignedInteger('odometer_km')->nullable();
            $table->unsignedInteger('usage_hours')->nullable();
            $table->string('insurance_company', 100)->nullable();
            $table->string('insurance_policy', 100)->nullable();
            $table->date('insurance_expires_at')->nullable();
            $table->date('inspection_expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_vehicle');
    }
};
