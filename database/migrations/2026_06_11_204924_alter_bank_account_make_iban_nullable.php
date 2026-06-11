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
            $table->string('iban', 34)->nullable()->change();
            $table->decimal('amount', 10, 3)->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('bank_account', function (Blueprint $table) {
            $table->string('iban', 34)->nullable(false)->change();
            $table->decimal('amount', 10, 3)->nullable(false)->change();
        });
    }
};
