<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Uuid;

class BankSeeder extends Seeder
{
    protected $command;

    public function __construct(Command $command)
    {
        $this->command = $command;
    }

    /**
     * Run the database seeds.
     * php artisan db:seed --class=BankSeeder
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        try {
            DB::table('bank')->truncate();
        } catch (QueryException $e) {
            // If truncating the table fails, try to delete records individually
            $this->command->info('Truncate failed. Trying to delete records individually.');

            // Delete records individually
            DB::table('bank')->delete();

            $this->command->info('Records deleted successfully.');
        }
        Schema::enableForeignKeyConstraints();

        $countries = ['al', 'ad', 'ee', 'mx', 'es', 'lt', 'gb', 'fr', 'de', 'pt', 'nl', 'ie'];

        foreach ($countries as $countryId) {
            $filePath = 'database/seeders/Bank/bank_'.$countryId.'.php';
            if (file_exists(base_path($filePath))) {
                $data = require_once base_path($filePath);

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
