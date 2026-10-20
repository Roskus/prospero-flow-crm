<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Quota of client companies per company, the parent company of each client company, and the default company
     * of users with multi-company access (null for everyone else).
     */
    public function up(): void
    {
        Schema::table('company', function (Blueprint $table) {
            $table->unsignedSmallInteger('managed_companies_limit')->default(0)->after('status');
            $table->unsignedBigInteger('parent_company_id')->nullable()->after('managed_companies_limit');

            $table->foreign('parent_company_id')->references('id')->on('company');
        });

        Schema::table('user', function (Blueprint $table) {
            $table->unsignedBigInteger('default_company_id')->nullable()->after('company_id');

            $table->foreign('default_company_id')->references('id')->on('company');
        });
    }

    public function down(): void
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropForeign(['default_company_id']);
            $table->dropColumn('default_company_id');
        });

        Schema::table('company', function (Blueprint $table) {
            $table->dropForeign(['parent_company_id']);
            $table->dropColumn(['managed_companies_limit', 'parent_company_id']);
        });
    }
};
