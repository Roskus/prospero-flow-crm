<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Who receives a campaign: every lead with opt in, or only the leads with any of its tags.
 * Existing campaigns keep sending to every lead, as they did before.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaign', function (Blueprint $table) {
            $table->string('audience', 10)->default('all')->after('tags');
        });
    }

    public function down(): void
    {
        Schema::table('campaign', function (Blueprint $table) {
            $table->dropColumn('audience');
        });
    }
};
