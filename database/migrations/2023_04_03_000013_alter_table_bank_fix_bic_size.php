<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bank', function (Blueprint $table) {
            $table->string('bic', 11)->nullable()->change(); //SWIFT
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //No rollback action
    }
};
