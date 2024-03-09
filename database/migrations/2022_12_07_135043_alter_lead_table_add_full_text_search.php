<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
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
            Schema::table('lead', function (Blueprint $table) {
                $table->fullText(['name', 'business_name'], 'lead_fulltext');
            });
        } else {
            // Output a warning indicating that the database driver does not support fulltext index creation
            Log::warning('Warning: Fulltext index creation is not supported by this database driver.');
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
            Schema::table('lead', function (Blueprint $table) {
                $table->dropIndex('lead_name_business_name_fulltext');
            });
        } else {
            // Output a warning indicating that the database driver does not support fulltext index creation
            Log::warning('Warning: Fulltext index creation is not supported by this database driver.');
        }
    }
};
