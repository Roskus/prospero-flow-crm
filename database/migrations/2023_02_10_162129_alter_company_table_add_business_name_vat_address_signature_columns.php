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
        //
        Schema::table('company', function (Blueprint $table) {
            $table->string('business_name', 80)->nullable()->after('name');
            $table->string('vat', 20)->nullable()->after('business_name');
            $table->string('province', 50)->nullable()->after('country_id');
            $table->string('city', 50)->nullable()->after('province');
            $table->string('street', 80)->nullable()->after('city');
            $table->string('zipcode', 10)->nullable()->after('street');
            $table->text('signature_html')->nullable()->after('logo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company', function (Blueprint $table) {
            $table->dropColumn('business_name');
            $table->dropColumn('vat');
            $table->dropColumn('province');
            $table->dropColumn('city');
            $table->dropColumn('street');
            $table->dropColumn('zipcode');
            $table->dropColumn('signature_html');
        });
    }
};
