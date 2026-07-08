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
        Schema::table('lead', function (Blueprint $table) {
            $table->string('external_id', 255)->nullable()->change();
        });

        Schema::table('customer', function (Blueprint $table) {
            $table->string('external_id', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lead', function (Blueprint $table) {
            $table->unsignedBigInteger('external_id')->nullable()->change();
        });

        Schema::table('customer', function (Blueprint $table) {
            $table->unsignedBigInteger('external_id')->nullable()->change();
        });
    }
};
