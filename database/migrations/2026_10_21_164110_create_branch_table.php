<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('company')->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 10);
            $table->boolean('is_main')->default(false);
            $table->address();
            $table->string('phone', 30)->nullable();
            $table->string('email', 191)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch');
    }
};
