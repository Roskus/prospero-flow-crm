<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('jobs') && ! Schema::hasTable('job')) {
            Schema::rename('jobs', 'job');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('job') && ! Schema::hasTable('jobs')) {
            Schema::rename('job', 'jobs');
        }
    }
};
