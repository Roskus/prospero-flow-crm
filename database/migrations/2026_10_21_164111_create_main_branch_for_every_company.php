<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Every company, including soft deleted ones, gets a main branch built from its own data.
 * Uses the query builder and its own column list so the migration does not depend on models or observers.
 */
return new class extends Migration
{
    private const array ADDRESS_COLUMNS = ['country_id', 'province', 'city', 'locality', 'street', 'address_extra', 'zipcode', 'latitude', 'longitude'];

    public function up(): void
    {
        DB::table('company')
            ->whereNotExists(fn ($query) => $query->from('branch')
                ->whereColumn('branch.company_id', 'company.id')
                ->where('branch.is_main', true))
            ->orderBy('id')
            ->chunkById(500, function ($companies): void {
                $now = now();

                DB::table('branch')->insert($companies->map(fn ($company): array => [
                    'company_id' => $company->id,
                    'name' => $company->name,
                    'code' => '0001',
                    'is_main' => true,
                    ...collect(self::ADDRESS_COLUMNS)->mapWithKeys(fn (string $column): array => [$column => $company->{$column}])->all(),
                    'phone' => $company->phone,
                    'email' => $company->email,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all());
            });
    }

    public function down(): void
    {
        DB::table('branch')->delete();
    }
};
