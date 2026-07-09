<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_schedule', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedTinyInteger('day_of_week'); // 1=Monday .. 7=Sunday
            $table->time('start_time');
            $table->time('end_time');
            $table->string('type', 20)->default('work'); // work, break
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('user')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_schedule');
    }
};
