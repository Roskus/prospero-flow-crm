<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Optional product weight and dimensions, each with its unit.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product', function (Blueprint $table) {
            $table->decimal('weight', 10, 3)->nullable()->after('quantity');
            $table->string('weight_unit', 5)->nullable()->default('g')->after('weight');
            $table->string('dimensions', 100)->nullable()->after('weight_unit');
            $table->string('dimension_unit', 5)->nullable()->default('mm')->after('dimensions');
        });
    }

    public function down(): void
    {
        Schema::table('product', function (Blueprint $table) {
            $table->dropColumn(['weight', 'weight_unit', 'dimensions', 'dimension_unit']);
        });
    }
};
