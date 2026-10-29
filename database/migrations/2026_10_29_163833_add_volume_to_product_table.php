<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Optional product volume (for liquids), kept apart from weight.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product', function (Blueprint $table) {
            $table->decimal('volume', 10, 3)->nullable()->after('weight_unit');
            $table->string('volume_unit', 5)->nullable()->default('ml')->after('volume');
        });
    }

    public function down(): void
    {
        Schema::table('product', function (Blueprint $table) {
            $table->dropColumn(['volume', 'volume_unit']);
        });
    }
};
