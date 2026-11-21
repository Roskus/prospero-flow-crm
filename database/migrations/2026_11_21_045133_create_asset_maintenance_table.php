<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_maintenance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('asset')->cascadeOnDelete();
            $table->string('type', 20);
            $table->date('date');
            $table->text('description');
            $table->decimal('cost', 12, 2)->nullable();
            $table->char('currency', 3)->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained('supplier')->nullOnDelete();
            $table->unsignedInteger('odometer_km')->nullable();
            $table->unsignedInteger('usage_hours')->nullable();
            $table->date('next_due_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('user')->nullOnDelete();
            $table->timestamps();

            $table->index(['asset_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_maintenance');
    }
};
