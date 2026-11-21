<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Previous ModuleSeeder runs inserted the base modules again on every run, so duplicates
     * are merged into the lowest id (the one permissions were seeded with) before adding the index.
     */
    public function up(): void
    {
        $duplicates = DB::table('module')
            ->select('name', DB::raw('MIN(id) as kept_id'))
            ->groupBy('name')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            $removedIds = DB::table('module')
                ->where('name', $duplicate->name)
                ->where('id', '<>', $duplicate->kept_id)
                ->pluck('id');

            DB::table('permission')->whereIn('module_id', $removedIds)->update(['module_id' => $duplicate->kept_id]);
            DB::table('module')->whereIn('id', $removedIds)->delete();
        }

        Schema::table('module', function (Blueprint $table) {
            $table->unique('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('module', function (Blueprint $table) {
            $table->dropUnique(['name']);
        });
    }
};
