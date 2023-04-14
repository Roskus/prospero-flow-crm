<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Uuid;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * php artisan db:seed --class=BankSeeder
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('bank')->truncate();
        Schema::enableForeignKeyConstraints();

        $countries = ['al', 'ad', 'ee', 'mx', 'es', 'lt', 'gb', 'fr', 'de', 'pt', 'nl', 'ie'];

        foreach ($countries as $countryId) {
            $filePath = 'database/seeders/Bank/bank_'.$countryId.'.php';
            if (file_exists(base_path($filePath))) {
                $data = require base_path($filePath);

                foreach ($data as $bank) {
                    $bank['uuid'] = Uuid::uuid5(Uuid::NAMESPACE_URL, $bank['bic'])->toString();
                    if (isset($bank['phone'])) {
                        $bank['phone'] = str_replace([' ', '-'], '', $bank['phone']);
                    }
                    DB::table('bank')->insert($bank);
                }
            }
        }
    }
}
