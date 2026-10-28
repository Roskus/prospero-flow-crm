<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Invoice lines keep the product barcode (EAN) at the moment of issue, like the SKU.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_item', function (Blueprint $table) {
            $table->string('barcode', 48)->nullable()->after('sku');
        });
    }

    public function down(): void
    {
        Schema::table('invoice_item', function (Blueprint $table) {
            $table->dropColumn('barcode');
        });
    }
};
