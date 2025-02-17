<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if the 'deleted_at' column does not exist
        if (! Schema::hasColumn('user', 'deleted_at')) {
            // If it doesn't exist, add the 'deleted_at' column
            Schema::table('user', function (Blueprint $table) {
                $table->softDeletes()->after('updated_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
    }
};
