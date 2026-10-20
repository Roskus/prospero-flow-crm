<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Extra companies a user can access besides their own. The legacy table from 2021 was never used
     * and is empty in production, so it is recreated instead of altered.
     */
    public function up(): void
    {
        Schema::dropIfExists('user_company');

        Schema::create('user_company', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('granted_by')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'user_id']);
            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('user')->cascadeOnDelete();
            $table->foreign('company_id')->references('id')->on('company')->cascadeOnDelete();
            $table->foreign('granted_by')->references('id')->on('user')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_company');

        Schema::create('user_company', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigInteger('user_id');
            $table->bigInteger('company_id');
        });
    }
};
