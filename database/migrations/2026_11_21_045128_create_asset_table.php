<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('company')->cascadeOnDelete();
            $table->string('category', 20);
            $table->string('type', 30);
            $table->string('status', 20)->default('available');
            $table->string('code', 50);
            $table->string('name');
            $table->string('brand', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->string('serial_number', 100)->nullable();
            $table->foreignId('branch_id')->nullable()->constrained('branch')->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('supplier')->nullOnDelete();
            $table->string('acquisition_type', 20)->default('purchase');
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 12, 2)->nullable();
            $table->decimal('rental_monthly_fee', 12, 2)->nullable();
            $table->date('rental_starts_at')->nullable();
            $table->date('rental_ends_at')->nullable();
            $table->char('currency', 3)->nullable();
            $table->date('warranty_until')->nullable();
            $table->date('next_maintenance_at')->nullable();
            $table->text('notes')->nullable();
            $table->string('photo_path')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'category', 'status']);
            // Codes are never reused, not even after a soft delete: they may be printed on labels.
            $table->unique(['company_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset');
    }
};
