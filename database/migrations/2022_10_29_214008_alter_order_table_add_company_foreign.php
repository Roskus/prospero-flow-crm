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
        Schema::disableForeignKeyConstraints();
        Schema::table('order', function (Blueprint $table) {
            $table->foreign('company_id', 'order_company_fk')->references('id')->on('company');
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('order', function (Blueprint $table) {
            $table->dropForeign('order_company_fk');
        });
        Schema::enableForeignKeyConstraints();
    }
};
