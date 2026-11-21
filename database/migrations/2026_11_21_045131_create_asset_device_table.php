<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_device', function (Blueprint $table) {
            $table->foreignId('asset_id')->primary()->constrained('asset')->cascadeOnDelete();
            $table->string('hostname', 100)->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->string('mac_address', 17)->nullable();
            $table->string('operating_system', 100)->nullable();
            $table->string('imei', 15)->nullable()->index();
            $table->foreignId('carrier_id')->nullable()->constrained('carrier')->nullOnDelete();
            $table->string('phone_number', 30)->nullable()->index();
            $table->text('sim_pin')->nullable(); // encrypted
            $table->text('sim_puk')->nullable(); // encrypted
            $table->string('cpu', 100)->nullable();
            $table->string('ram', 50)->nullable();
            $table->string('storage', 100)->nullable();
            $table->text('specs')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_device');
    }
};
