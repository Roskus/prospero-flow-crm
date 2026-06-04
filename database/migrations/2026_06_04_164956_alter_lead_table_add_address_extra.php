<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lead', function (Blueprint $table) {
            $table->string('address_extra', 80)->nullable()->after('street');
        });
    }

    public function down(): void
    {
        Schema::table('lead', function (Blueprint $table) {
            $table->dropColumn('address_extra');
        });
    }
};
