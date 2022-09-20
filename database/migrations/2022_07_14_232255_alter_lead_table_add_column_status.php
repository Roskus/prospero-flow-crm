<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLeadTableAddColumnStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lead', function (Blueprint $table) {
            $table->string('linkedin')->nullable()->after('website');
            $table->string('facebook')->nullable()->after('linkedin');
            $table->string('instagram')->nullable()->after('facebook');
            $table->string('twitter')->nullable()->after('instagram');
            $table->string('youtube')->nullable()->after('twitter');
            $table->string('tiktok')->nullable()->after('youtube');
            $table->enum('status', ['open', 'first_contact', 'recall', 'quote', 'quoted', 'waiting_for_answer', 'standby', 'closed'])->nullable()->default('open')->after('country_id');
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
            $table->dropColumn('linkedin');
            $table->dropColumn('facebook');
            $table->dropColumn('instagram');
            $table->dropColumn('twitter');
            $table->dropColumn('youtube');
            $table->dropColumn('tiktok');
            $table->dropColumn('status');
        });
    }
}
