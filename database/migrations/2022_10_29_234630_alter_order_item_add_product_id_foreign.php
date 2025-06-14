<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('order_item', function (Blueprint $table) {
                $table->foreign('product_id', 'order_item_product_fk')->references('id')->on('product');
            });
        }
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
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('order_item', function (Blueprint $table) {
                $table->dropForeign('order_item_product_fk');
            });
        }
        Schema::enableForeignKeyConstraints();
    }
};
