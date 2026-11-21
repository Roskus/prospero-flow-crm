<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_assignment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('asset')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('user')->cascadeOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('user')->nullOnDelete();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['asset_id', 'end_date']);
            $table->index(['user_id', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_assignment');
    }
};
