<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop old tinyint status before adding the enum replacement
        Schema::table('account', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('account', function (Blueprint $table) {
            $table->enum('type', ['income', 'expense'])->after('name');
            $table->unsignedBigInteger('account_category_id')->nullable()->after('type');
            $table->date('due_date')->nullable()->after('issue_date');
            $table->date('payment_date')->nullable()->after('due_date');
            $table->string('reference', 80)->nullable()->after('payment_date');
            $table->unsignedBigInteger('customer_id')->nullable()->after('reference');
            $table->unsignedBigInteger('supplier_id')->nullable()->after('customer_id');
            $table->text('notes')->nullable()->after('supplier_id');
            $table->enum('status', ['pending', 'paid', 'overdue'])->default('pending')->after('amount');

            $table->foreign('account_category_id')->references('id')->on('account_category')->nullOnDelete();
            $table->foreign('customer_id')->references('id')->on('customer')->nullOnDelete();
            $table->foreign('supplier_id')->references('id')->on('supplier')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('account', function (Blueprint $table) {
            $table->dropForeign(['account_category_id']);
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['supplier_id']);
            $table->dropColumn([
                'type', 'account_category_id', 'status', 'due_date', 'payment_date',
                'reference', 'customer_id', 'supplier_id', 'notes',
            ]);
            $table->unsignedTinyInteger('status')->default(1)->after('amount');
        });
    }
};
