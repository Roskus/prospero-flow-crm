<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLeadAddFieldCityStreetZipcode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lead', function (Blueprint $table) {
            $table->string('city', 50)->nullable()->after('country_id');
            $table->string('street', 80)->nullable()->after('city');
            $table->string('zipcode', 10)->nullable()->after('street');
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
            $table->dropColumn('city');
            $table->dropColumn('street');
            $table->dropColumn('zipcode');
        });
    }
}
