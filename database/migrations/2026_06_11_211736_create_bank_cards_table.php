<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_card', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('bank_account_id');
            $table->enum('type', ['debit', 'credit']);
            $table->enum('network', ['visa', 'mastercard', 'amex', 'other']);
            $table->string('cardholder_name', 80);
            $table->text('number_encrypted');
            $table->char('last_four', 4);
            $table->text('cvv_encrypted')->nullable();
            $table->unsignedTinyInteger('expires_month');
            $table->unsignedSmallInteger('expires_year');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('company');
            $table->foreign('bank_account_id')->references('id')->on('bank_account');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_card');
    }
};
