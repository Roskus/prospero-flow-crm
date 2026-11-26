<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_machinery', function (Blueprint $table) {
            $table->foreignId('asset_id')->primary()->constrained('asset')->cascadeOnDelete();
            $table->string('capacity', 50)->nullable();
            $table->string('certification_number', 100)->nullable();
            $table->string('certified_by', 100)->nullable();
            $table->date('certification_expires_at')->nullable();
            $table->date('calibration_expires_at')->nullable();
            $table->date('safety_inspection_expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_machinery');
    }
};
