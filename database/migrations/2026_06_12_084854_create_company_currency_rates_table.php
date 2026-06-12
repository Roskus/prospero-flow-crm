<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_currency_rate', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('company')->cascadeOnDelete();
            $table->string('currency', 3);
            $table->decimal('conversion_rate', 16, 6);
            $table->timestamps();

            $table->unique(['company_id', 'currency']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_currency_rate');
    }
};
