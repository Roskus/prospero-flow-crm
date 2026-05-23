<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_item', function (Blueprint $table) {
            $table->decimal('discount', 4, 2)->default(0)->comment('%')->change();
            $table->decimal('tax', 4, 2)->default(0)->comment('%')->change();
        });
    }

    public function down(): void
    {
        Schema::table('order_item', function (Blueprint $table) {
            $table->decimal('discount', 4, 2)->nullable(false)->comment('%')->change();
            $table->decimal('tax', 4, 2)->nullable(false)->comment('%')->change();
        });
    }
};
