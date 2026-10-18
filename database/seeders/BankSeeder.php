<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class BankSeeder extends Seeder
{
    /**
     * Loads every database/seeders/Bank/bank_<country>.php file. Idempotent: banks are matched
     * by a UUID derived from their BIC, so existing rows (and the bank accounts that reference
     * their ids) are kept and only updated.
     * php artisan db:seed --class=BankSeeder
     */
    public function run(): void
    {
        $banks = [];

        foreach (glob(database_path('seeders/Bank/bank_*.php')) as $filePath) {
            foreach (require $filePath as $bank) {
                $banks[] = [
                    'uuid' => Uuid::uuid5(Uuid::NAMESPACE_URL, $bank['bic'])->toString(),
                    'country_id' => $bank['country_id'],
                    'name' => $bank['name'],
                    'bic' => $bank['bic'],
                    'phone' => isset($bank['phone']) ? str_replace([' ', '-'], '', $bank['phone']) : null,
                    'email' => $bank['email'] ?? null,
                    'website' => $bank['website'] ?? null,
                ];
            }
        }

        Bank::query()->upsert($banks, ['uuid'], ['country_id', 'name', 'bic', 'phone', 'email', 'website']);
    }
}
