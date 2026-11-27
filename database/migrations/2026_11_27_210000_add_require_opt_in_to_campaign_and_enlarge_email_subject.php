<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Campaigns can be sent to contacts without opt in (existing campaigns keep requiring it),
     * and email subjects allow 120 characters.
     */
    public function up(): void
    {
        Schema::table('campaign', function (Blueprint $table) {
            $table->boolean('require_opt_in')->default(true)->after('audience');
        });

        Schema::table('email', function (Blueprint $table) {
            $table->string('subject', 120)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaign', function (Blueprint $table) {
            $table->dropColumn('require_opt_in');
        });

        Schema::table('email', function (Blueprint $table) {
            $table->string('subject', 100)->change();
        });
    }
};
