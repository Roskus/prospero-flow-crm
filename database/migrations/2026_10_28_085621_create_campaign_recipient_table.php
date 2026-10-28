<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * One row per email sent by a campaign, with its result and opens.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_recipient', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaign')->cascadeOnDelete();
            $table->unsignedBigInteger('lead_id')->nullable();
            $table->string('email', 254);
            $table->string('status', 20);
            $table->text('error')->nullable();
            $table->dateTime('sent_at');
            $table->dateTime('opened_at')->nullable();
            $table->unsignedInteger('open_count')->default(0);
            $table->timestamps();

            $table->unique(['campaign_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_recipient');
    }
};
