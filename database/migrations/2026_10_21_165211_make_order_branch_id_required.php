<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->nullable(false)->change();
            $table->foreign('branch_id')->references('id')->on('branch');
        });
    }

    public function down(): void
    {
        Schema::table('order', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->unsignedBigInteger('branch_id')->nullable()->change();
        });
    }
};
