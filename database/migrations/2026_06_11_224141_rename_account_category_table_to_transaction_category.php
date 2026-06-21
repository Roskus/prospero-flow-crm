<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::rename('account_category', 'transaction_category');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('transaction_category', 'account_category');
    }
};
