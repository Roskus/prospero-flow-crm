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
        Schema::create(
            'company', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('logo', 191)->nullable();
                $table->string('phone', 30)->nullable();
                $table->string('email', 191)->nullable();
                $table->string('country_id', 2)->nullable(); // Squire\Models\Country->code_2 ISO 3166-1 alpha-2 country code
                $table->string('website', 191)->nullable();
                $table->timestamps();
                $table->unsignedTinyInteger('status');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company');
    }
};
