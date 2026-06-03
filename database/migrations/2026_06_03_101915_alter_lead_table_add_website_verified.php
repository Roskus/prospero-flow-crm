<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lead', function (Blueprint $table) {
            $table->boolean('website_verified')->nullable()->after('website');
        });
    }

    public function down(): void
    {
        Schema::table('lead', function (Blueprint $table) {
            $table->dropColumn('website_verified');
        });
    }
};
