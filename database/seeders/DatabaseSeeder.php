<?php

declare(strict_types=1);

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class DatabaseSeeder extends Seeder
{
    //use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        switch (App::environment()) {
            case 'local':
                $this->call(CompanyTableSeeder::class);
                $this->call(RoleSeeder::class);
                $this->call(UserSeeder::class);
                $this->call(IndustrySeeder::class);
                $this->call(BankSeeder::class);
                $this->call(TicketSeeder::class);
                break;
            case 'testing':
                //
                break;
            case 'production':
                //
                break;
        }
    }
}
