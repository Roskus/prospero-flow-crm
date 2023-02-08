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
    public function up(): void
    {
        if (! Schema::hasTable('ticket_message')) {
            Schema::create('ticket_message', function (Blueprint $table): void {
                $table->id();
                $table->text('body');
                $table->unsignedBigInteger('author_id');
                $table->unsignedBigInteger('ticket_id');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_message');
    }
};
