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
        Schema::create('ticket', function (Blueprint $table) {
            $table->id();
            $table->string('title', 80);
            $table->text('description');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->enum('type', ['', 'question', 'incident', 'issue'])->default('');
            $table->enum('status', ['new', 'assigned', 'duplicated', 'closed'])->default('new');
            $table->timestamps();
            $table->dateTime('closed_at')->nullable();
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
        Schema::dropIfExists('ticket');
    }
};
