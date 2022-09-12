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
        Schema::table('email', function (Blueprint $table) {
            $table->enum('status', ['draft', 'queue', 'sent', 'error'])->default('draft')->after('lang');
            $table->dateTime('schedule_send')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('email', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('schedule_send');
        });
    }
};
