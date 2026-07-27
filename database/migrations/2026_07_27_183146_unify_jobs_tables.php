<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('job');

        DB::statement('RENAME TABLE jobs TO job');
    }

    public function down(): void
    {
        DB::statement('RENAME TABLE job TO jobs');
    }
};
