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
            $table->foreignId('branch_id')->nullable()->after('company_id')->constrained('branch')->nullOnDelete();
            $table->boolean('show_on_invoice')->default(false)->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('bank_account', function (Blueprint $table) {
            $table->dropConstrainedForeignId('branch_id');
            $table->dropColumn('show_on_invoice');
        });
    }
};
