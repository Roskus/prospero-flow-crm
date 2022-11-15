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
        DB::table('industry')->insert(['name' => 'Aerospace']);
        DB::table('industry')->insert(['name' => 'Advertising']);
        DB::table('industry')->insert(['name' => 'Agricultural']);
        DB::table('industry')->insert(['name' => 'Alimentation and drinks']);
        DB::table('industry')->insert(['name' => 'Architecture']);
        DB::table('industry')->insert(['name' => 'Automotive']);
        DB::table('industry')->insert(['name' => 'Apparel & Accessories']);

        DB::table('industry')->insert(['name' => 'Banking']);
        DB::table('industry')->insert(['name' => 'Biotechnology']);
        DB::table('industry')->insert(['name' => 'Building Materials & Equipment']);

        DB::table('industry')->insert(['name' => 'Computer']);
        DB::table('industry')->insert(['name' => 'Construction']);
        DB::table('industry')->insert(['name' => 'Consulting']);
        DB::table('industry')->insert(['name' => 'Creative']);
        DB::table('industry')->insert(['name' => 'Culture']);
        DB::table('industry')->insert(['name' => 'Chemical']);

        DB::table('industry')->insert(['name' => 'Education']);
        DB::table('industry')->insert(['name' => 'Electronics']);
        DB::table('industry')->insert(['name' => 'Energy']);
        DB::table('industry')->insert(['name' => 'Entertainment & Leisure']);

        DB::table('industry')->insert(['name' => 'Finance']);

        DB::table('industry')->insert(['name' => 'Healthcare']);
        DB::table('industry')->insert(['name' => 'Hospitality']);

        DB::table('industry')->insert(['name' => 'Insurance']);

        DB::table('industry')->insert(['name' => 'Legal']);

        DB::table('industry')->insert(['name' => 'Manufacturing']);
        DB::table('industry')->insert(['name' => 'Marketing']);
        DB::table('industry')->insert(['name' => 'Mass Media']);
        DB::table('industry')->insert(['name' => 'Mining']);
        DB::table('industry')->insert(['name' => 'Music']);

        DB::table('industry')->insert(['name' => 'Petroleum']);
        DB::table('industry')->insert(['name' => 'Publishing']);

        DB::table('industry')->insert(['name' => 'Real Estate']);
        DB::table('industry')->insert(['name' => 'Retail']);

        DB::table('industry')->insert(['name' => 'Service']);
        DB::table('industry')->insert(['name' => 'Shipping']);
        DB::table('industry')->insert(['name' => 'Software']);
        DB::table('industry')->insert(['name' => 'Sports']);
        DB::table('industry')->insert(['name' => 'Support']);

        DB::table('industry')->insert(['name' => 'Technology']);
        DB::table('industry')->insert(['name' => 'Telecommunications']);
        DB::table('industry')->insert(['name' => 'Television']);
        DB::table('industry')->insert(['name' => 'Testing, Inspection & Certification']);
        DB::table('industry')->insert(['name' => 'Transportation']);
        DB::table('industry')->insert(['name' => 'Travel']);

        DB::table('industry')->insert(['name' => 'Venture Capital']);
        DB::table('industry')->insert(['name' => 'Water']);
        DB::table('industry')->insert(['name' => 'Wholesale']);
    }
}
