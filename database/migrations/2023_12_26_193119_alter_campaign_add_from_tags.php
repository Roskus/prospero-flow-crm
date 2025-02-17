<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('campaign', function (Blueprint $table) {
            $table->string('from', 254)->after('subject');
            $table->json('tags')->after('body')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaign', function (Blueprint $table) {
            $table->dropColumn('from');
            $table->dropColumn('tags');
        });
    }
};
