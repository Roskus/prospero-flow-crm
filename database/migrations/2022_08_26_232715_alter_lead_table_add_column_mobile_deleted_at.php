<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLeadTableAddColumnMobileDeletedAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lead', function (Blueprint $table) {
            $table->string('mobile', 15)->nullable()->after('phone');
            $table->softDeletes()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lead', function (Blueprint $table) {
            $table->dropColumn('mobile');
            $table->dropColumn('deleted_at');
        });

    }
}
