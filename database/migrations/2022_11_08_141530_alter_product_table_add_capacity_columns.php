<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product', function (Blueprint $table) {
            $table->decimal('capacity', 10, 2)->nullable()->after('quantity');
            $table->enum('capacity_measure', ['', 'l', 'dl', 'cl', 'ml', 'kg', 'g', 'mg'])->after('capacity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product', function (Blueprint $table) {
            $table->dropColumn('capacity');
            $table->dropColumn('capacity_measure');
        });
    }
};
