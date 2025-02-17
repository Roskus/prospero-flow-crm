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
        Schema::create('campaign', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id');
            $table->string('subject', 255);
            $table->text('body')->nullable();
            $table->date('schedule_send_date')->nullable();
            $table->timeTz('schedule_send_time')->nullable();
            $table->dateTime('send_at')->nullable();
            $table->bigInteger('emails_count')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('campaign');
    }
};
