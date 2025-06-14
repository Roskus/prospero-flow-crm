<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('ticket', function (Blueprint $table) {
                $table->string('priority', 20)->change();
                $table->string('type', 20)->change();
            });
        } else {
            // For sqlite, recreate the table with all columns
            Schema::rename('ticket', 'ticket_old');
            Schema::create('ticket', function (Blueprint $table) {
                $table->id();
                $table->string('title', 80);
                $table->text('description');
                $table->unsignedBigInteger('company_id');
                $table->unsignedBigInteger('customer_id')->nullable();
                $table->unsignedBigInteger('created_by');
                $table->unsignedBigInteger('assigned_to')->nullable();
                $table->string('priority', 20)->default('normal');
                $table->string('type', 20)->default('');
                $table->enum('status', ['new', 'assigned', 'duplicated', 'closed'])->default('new');
                $table->timestamps();
                $table->dateTime('closed_at')->nullable();
                $table->softDeletes();
            });
            // Copy data from old table to new table
            DB::statement('INSERT INTO ticket (id, title, description, company_id, customer_id, created_by, assigned_to, priority, type, status, created_at, updated_at, closed_at, deleted_at) SELECT id, title, description, company_id, customer_id, created_by, assigned_to, priority, type, status, created_at, updated_at, closed_at, deleted_at FROM ticket_old');
            Schema::drop('ticket_old');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket', function (Blueprint $table) {
            // No rollback
        });
    }
};
