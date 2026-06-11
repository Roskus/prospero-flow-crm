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
            $table->string('type', 30)->default('bank')->after('company_id');
            $table->string('account_name', 80)->nullable()->after('type');
            $table->unsignedBigInteger('bank_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('bank_account', function (Blueprint $table) {
            $table->dropColumn(['type', 'account_name']);
            $table->unsignedBigInteger('bank_id')->nullable(false)->change();
        });
    }
};
