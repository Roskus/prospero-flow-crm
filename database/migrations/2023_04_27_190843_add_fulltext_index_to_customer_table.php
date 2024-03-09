<?php

use Illuminate\Console\Command;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected Command $command;

    public function __construct(Command $command)
    {
        $this->command = $command;
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if the database driver supports fulltext index creation (e.g., MySQL or PostgreSQL)
        $supportedDrivers = ['mysql', 'pgsql'];
        $currentDriver = config('database.default');

        if (in_array($currentDriver, $supportedDrivers)) {
            Schema::table('customer', function (Blueprint $table) {
                $table->fullText(['name', 'business_name'], 'customer_fulltext');
            });
        } else {
            // Output a warning indicating that the database driver does not support fulltext index creation
            $this->command->info('Warning: Fulltext index creation is not supported by this database driver.');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Check if the database driver supports fulltext index creation
        $supportedDrivers = ['mysql', 'pgsql'];
        $currentDriver = config('database.default');

        if (in_array($currentDriver, $supportedDrivers)) {
            Schema::table('customer', function (Blueprint $table) {
                $table->dropIndex('customer_fulltext');
            });
        } else {
            // Output a warning indicating that the database driver does not support fulltext index creation
            $this->command->info('Warning: Fulltext index creation is not supported by this database driver.');
        }
    }
};
