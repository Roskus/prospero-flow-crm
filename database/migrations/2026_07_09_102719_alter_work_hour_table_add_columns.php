<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_hour', function (Blueprint $table) {
            $table->string('type', 20)->default('work')->after('end_time'); // work, break, other
            $table->boolean('is_manual')->default(false)->after('type');
            $table->text('notes')->nullable()->after('is_manual');
            $table->string('ip_address', 45)->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('work_hour', function (Blueprint $table) {
            $table->dropColumn(['type', 'is_manual', 'notes', 'ip_address']);
        });
    }
};
