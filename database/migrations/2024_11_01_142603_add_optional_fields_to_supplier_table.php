<?php

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
        Schema::table('supplier', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('zipcode');
            $table->string('account_number')->nullable()->after('notes');
            $table->string('order_url', 255)->nullable()->after('account_number');
            $table->string('order_user', 254)->nullable()->after('order_url');
            $table->string('order_password', 20)->nullable()->after('order_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier', function (Blueprint $table) {
            $table->dropColumn(['notes', 'account_number', 'order_url', 'order_user', 'order_password']);
        });
    }
};
