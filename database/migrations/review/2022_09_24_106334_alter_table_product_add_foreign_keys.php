<?php

declare(strict_types=1);

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
            $table->foreign('company_id')->on('company')->references('id');
            $table->foreign('category_id')->on('category')->references('id');
            $table->foreign('brand_id')->on('brand')->references('id');
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
            $table->dropIndex('company_id');
            $table->dropIndex('category_id');
            $table->dropIndex('brand_id');
        });
    }
};
