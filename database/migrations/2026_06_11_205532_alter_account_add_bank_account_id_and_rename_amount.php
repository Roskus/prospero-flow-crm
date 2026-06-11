<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add bank_account_id to accounting transactions
        Schema::table('account', function (Blueprint $table) {
            $table->unsignedBigInteger('bank_account_id')->nullable()->after('account_category_id');
            $table->foreign('bank_account_id')->references('id')->on('bank_account')->nullOnDelete();
        });

        // Rename amount to opening_balance in bank_account
        Schema::table('bank_account', function (Blueprint $table) {
            $table->renameColumn('amount', 'opening_balance');
        });
    }

    public function down(): void
    {
        Schema::table('account', function (Blueprint $table) {
            $table->dropForeign(['bank_account_id']);
            $table->dropColumn('bank_account_id');
        });

        Schema::table('bank_account', function (Blueprint $table) {
            $table->renameColumn('opening_balance', 'amount');
        });
    }
};
