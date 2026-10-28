<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Campaigns get a name to tell them apart; existing campaigns take their subject.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaign', function (Blueprint $table) {
            $table->string('name', 255)->nullable()->after('company_id');
        });

        DB::table('campaign')->whereNull('name')->update(['name' => DB::raw('subject')]);

        Schema::table('campaign', function (Blueprint $table) {
            $table->string('name', 255)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('campaign', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }
};
