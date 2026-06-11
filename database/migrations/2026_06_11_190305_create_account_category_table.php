<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_category', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('name', 80);
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('company');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_category');
    }
};
