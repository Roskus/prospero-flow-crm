<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bank_account', function (Blueprint $table) {
            $table->string('bizum', 15)->nullable()->after('alias');
        });
    }

    public function down(): void
    {
        Schema::table('bank_account', function (Blueprint $table) {
            $table->dropColumn('bizum');
        });
    }
};
