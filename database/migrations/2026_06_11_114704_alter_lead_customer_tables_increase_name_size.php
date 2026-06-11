<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lead', function (Blueprint $table) {
            $table->string('name', 120)->change();
            $table->string('business_name', 120)->nullable()->change();
        });

        Schema::table('customer', function (Blueprint $table) {
            $table->string('name', 120)->change();
            $table->string('business_name', 120)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('lead', function (Blueprint $table) {
            $table->string('name', 80)->change();
            $table->string('business_name', 80)->nullable()->change();
        });

        Schema::table('customer', function (Blueprint $table) {
            $table->string('name', 80)->change();
            $table->string('business_name', 80)->nullable()->change();
        });
    }
};
