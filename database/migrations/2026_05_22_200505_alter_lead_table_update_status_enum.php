<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `lead` MODIFY COLUMN `status` ENUM('open','in_progress','waiting_feedback','converted','closed') NULL DEFAULT 'open'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `lead` MODIFY COLUMN `status` ENUM('open','first_contact','recall','quote','quoted','waiting_for_answer','standby','closed') NULL DEFAULT 'open'");
    }
};
