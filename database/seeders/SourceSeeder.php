<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * php artisan db:seed --class=SourceSeeder
     */
    public function run(): void
    {
        DB::table('source')->truncate();

        DB::table('source')->insert(['name' => 'Cold call']);
        DB::table('source')->insert(['name' => 'Online paid campaign']);
        DB::table('source')->insert(['name' => 'Online funnel']);
        DB::table('source')->insert(['name' => 'Social media']);
        DB::table('source')->insert(['name' => 'Newsletter']);
        DB::table('source')->insert(['name' => 'Recommendation']);
    }
}
