<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('email_attach', function (Blueprint $table) {
            if (Schema::hasColumn('email_attach', 'deleted_at')) {
                $table->dropColumn('deleted_at');
            }
            $table->unsignedBigInteger('email_id')->change();
        });

        Schema::table('email_attach', function (Blueprint $table) {
            if (! Schema::hasColumn('email_attach', 'original_name')) {
                $table->string('original_name', 255)->after('file');
            }

            if (! Schema::hasColumn('email_attach', 'created_at')) {
                $table->timestamps();
            }
            $table->softDeletes();

            $table->foreign('email_id')->references('id')->on('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_attach', function (Blueprint $table) {
            $table->dropColumn('original_name');
            $table->dropTimestamps();
        });
    }
};
