<?php

declare(strict_types=1);

namespace Database\Seeders;

use App;
use Illuminate\Database\Seeder;

//use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    //use WithoutModelEvents;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        switch (App::environment()) {
            case 'local':

                $this->call(LocalDatabaseSeeder::class);

                break;
            case 'testing':

                $this->call(TestingDatabaseSeeder::class);

                break;
            case 'production':

                $this->call(ProductionDatabaseSeeder::class);

                break;
        }
    }
}
