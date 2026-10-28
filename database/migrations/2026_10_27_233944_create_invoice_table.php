<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('company')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branch')->restrictOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('order')->nullOnDelete();
            $table->foreignId('customer_id')->constrained('customer')->restrictOnDelete();
            $table->string('series', 10);
            $table->unsignedInteger('number');
            $table->date('issue_date');
            $table->date('due_date');
            $table->string('currency', 3);
            $table->decimal('exchange_rate', 16, 6)->default(1);
            $table->decimal('subtotal', 18, 5)->default(0);
            $table->decimal('tax', 18, 5)->default(0);
            $table->decimal('total', 18, 5)->default(0);
            $table->decimal('paid_amount', 18, 5)->default(0);
            $table->string('status', 20);
            $table->timestamp('cancelled_at')->nullable();
            $table->json('customer_data');
            $table->json('issuer_data');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('user')->nullOnDelete();
            $table->timestamps();

            $table->unique(['branch_id', 'number']);
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'issue_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice');
    }
};
