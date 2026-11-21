<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carrier', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('company')->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('country_id', 2)->nullable(); // Squire\Models\Country->code_2 ISO 3166-1 alpha-2 country code
            $table->string('phone', 30)->nullable();
            $table->string('website')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'name']);
            $table->index(['company_id', 'country_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carrier');
    }
};
