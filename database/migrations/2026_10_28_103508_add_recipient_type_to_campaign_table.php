<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Campaigns can be sent to leads, customers or both; existing campaigns keep sending to leads.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaign', function (Blueprint $table) {
            $table->string('recipient_type', 10)->default('leads')->after('audience');
        });

        Schema::table('campaign_recipient', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable()->after('lead_id');
        });
    }

    public function down(): void
    {
        Schema::table('campaign_recipient', function (Blueprint $table) {
            $table->dropColumn('customer_id');
        });

        Schema::table('campaign', function (Blueprint $table) {
            $table->dropColumn('recipient_type');
        });
    }
};
