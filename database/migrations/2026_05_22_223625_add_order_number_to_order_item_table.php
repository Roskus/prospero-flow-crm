<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_item', function (Blueprint $table) {
            $table->unsignedInteger('order_number')->nullable()->after('order_id');
        });

        DB::statement('
            UPDATE order_item oi
            JOIN `order` o ON o.id = oi.order_id
            SET oi.order_number = o.order_number
        ');
    }

    public function down(): void
    {
        Schema::table('order_item', function (Blueprint $table) {
            $table->dropColumn('order_number');
        });
    }
};
