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
        Schema::table('customer', function (Blueprint $table) {
            if (Schema::hasColumn('customer', 'company')) {
                $table->dropColumn('company');
            }
        });

        Schema::table('customer', function (Blueprint $table) {
            $table->date('dob')->nullable()->after('business_name');
            $table->string('vat', 20)->nullable()->after('business_name');
            $table->string('mobile', 15)->nullable()->after('phone');
            $table->string('linkedin')->nullable()->after('website');
            $table->string('facebook')->nullable()->after('linkedin');
            $table->string('instagram')->nullable()->after('facebook');
            $table->string('twitter')->nullable()->after('instagram');
            $table->string('youtube')->nullable()->after('twitter');
            $table->string('tiktok')->nullable()->after('youtube');
            $table->text('notes')->nullable()->after('website');
            $table->unsignedBigInteger('seller_id')->nullable()->after('notes');
            $table->string('province', 80)->nullable()->after('country_id');
            $table->string('city', 50)->nullable()->after('province');
            $table->string('locality', 80)->nullable()->after('city');
            $table->string('street', 80)->nullable()->after('locality');
            $table->string('zipcode', 10)->nullable()->after('street');
            $table->dateTime('schedule_contact')->nullable()->after('zipcode');
            $table->unsignedBigInteger('industry_id')->nullable()->after('schedule_contact');
            $table->boolean('opt_in')->nullable()->default(true)->after('industry_id');
            // $table->enum('status', ['open', 'recall', 'quote', 'quoted', 'waiting_for_answer', 'standby', 'closed'])->nullable()->default('open')->after('country_id');
            $table->string('status', 20)->default('No contacted')->after('country_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer', function (Blueprint $table) {
            if (Schema::hasColumn('customer', 'dob')) {
                $table->dropColumn('dob');
            }

            if (Schema::hasColumn('customer', 'vat')) {
                $table->dropColumn('vat');
            }

            if (Schema::hasColumn('customer', 'mobile')) {
                $table->dropColumn('mobile');
            }

            if (Schema::hasColumn('customer', 'linkedin')) {
                $table->dropColumn('linkedin');
            }

            if (Schema::hasColumn('customer', 'facebook')) {
                $table->dropColumn('facebook');
            }

            if (Schema::hasColumn('customer', 'instagram')) {
                $table->dropColumn('instagram');
            }

            if (Schema::hasColumn('customer', 'twitter')) {
                $table->dropColumn('twitter');
            }

            if (Schema::hasColumn('customer', 'youtube')) {
                $table->dropColumn('youtube');
            }

            if (Schema::hasColumn('customer', 'tiktok')) {
                $table->dropColumn('tiktok');
            }

            if (Schema::hasColumn('customer', 'notes')) {
                $table->dropColumn('notes');
            }

            if (Schema::hasColumn('customer', 'seller_id')) {
                $table->dropColumn('seller_id');
            }

            if (Schema::hasColumn('customer', 'province')) {
                $table->dropColumn('province');
            }

            if (Schema::hasColumn('customer', 'city')) {
                $table->dropColumn('city');
            }

            if (Schema::hasColumn('customer', 'locality')) {
                $table->dropColumn('locality');
            }

            if (Schema::hasColumn('customer', 'street')) {
                $table->dropColumn('street');
            }

            if (Schema::hasColumn('customer', 'zipcode')) {
                $table->dropColumn('zipcode');
            }

            if (Schema::hasColumn('customer', 'schedule_contact')) {
                $table->dropColumn('schedule_contact');
            }

            if (Schema::hasColumn('customer', 'industry_id')) {
                $table->dropColumn('industry_id');
            }

            if (Schema::hasColumn('customer', 'opt_in')) {
                $table->dropColumn('opt_in');
            }

            if (Schema::hasColumn('customer', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
