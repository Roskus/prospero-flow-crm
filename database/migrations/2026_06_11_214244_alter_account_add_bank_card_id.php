<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('account', function (Blueprint $table) {
            $table->unsignedBigInteger('bank_card_id')->nullable()->after('bank_account_id');
            $table->foreign('bank_card_id')->references('id')->on('bank_card')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('account', function (Blueprint $table) {
            $table->dropForeign(['bank_card_id']);
            $table->dropColumn('bank_card_id');
        });
    }
};
