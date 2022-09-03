<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterContactTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contact', function (Blueprint $table) {
            $table->string('linkedin', 255)->nullable()->after('email');
            $table->text('notes')->nullable()->after('country');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contact', function (Blueprint $table) {
            $table->dropColumn('linkedin');
            $table->dropColumn('notes');
        });
    }
}
