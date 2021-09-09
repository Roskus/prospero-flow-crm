<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user')->insert(
            [
                'company_id' => 1,
                'first_name' => 'Admin',
                'last_name' => 'Test',
                'email' => 'admin@admin.com',
                'password' => Hash::make('admin'),
                'lang' => 'en'
            ]
        );
    }
}
