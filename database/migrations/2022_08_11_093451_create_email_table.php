<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id');
            $table->bigInteger('template_id')->nullable();
            $table->decimal('version', 2, 1)->nullable();
            $table->string('from', 255);
            $table->string('to', 255);
            $table->string('cc', 255)->nullable();
            $table->string('subject', 78); // RFC2822 http://www.faqs.org/rfcs/rfc2822.html
            $table->text('body');
            $table->char('lang', 2)->default('en');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email');
    }
}
