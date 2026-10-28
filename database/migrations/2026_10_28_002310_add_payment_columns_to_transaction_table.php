<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaction', function (Blueprint $table) {
            $table->foreignId('invoice_id')->nullable()->after('supplier_id')->constrained('invoice')->restrictOnDelete();
            $table->string('currency', 3)->nullable()->after('amount');
            $table->decimal('exchange_rate', 16, 6)->default(1)->after('currency');
            $table->decimal('invoice_amount', 18, 5)->nullable()->after('exchange_rate');
            $table->string('payment_method', 20)->nullable()->after('invoice_amount');
        });
    }

    public function down(): void
    {
        Schema::table('transaction', function (Blueprint $table) {
            $table->dropConstrainedForeignId('invoice_id');
            $table->dropColumn(['currency', 'exchange_rate', 'invoice_amount', 'payment_method']);
        });
    }
};
