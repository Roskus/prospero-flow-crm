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
            $table->string('account_number', 30)->nullable()->after('iban');
            $table->string('swift', 11)->nullable()->after('account_number');
            $table->string('sort_code', 8)->nullable()->after('swift');
            $table->string('cbu', 22)->nullable()->after('sort_code');
            $table->string('alias', 60)->nullable()->after('cbu');
        });
    }

    public function down(): void
    {
        Schema::table('bank_account', function (Blueprint $table) {
            $table->dropColumn(['account_number', 'swift', 'sort_code', 'cbu', 'alias']);
        });
    }
};
