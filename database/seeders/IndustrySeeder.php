<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IndustrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('industry')->insert(['id' => 1, 'name' => 'Aerospace']);
        DB::table('industry')->insert(['id' => 2, 'name' => 'Agricultural']);
        DB::table('industry')->insert(['id' => 3, 'name' => 'Water']);
        DB::table('industry')->insert(['id' => 4, 'name' => 'Alimentation and drinks']);
        DB::table('industry')->insert(['id' => 5, 'name' => 'Architecture']);
        DB::table('industry')->insert(['id' => 6, 'name' => 'Automotive']);

    }
}
