<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Carrier;
use App\Models\Company;
use Illuminate\Database\Seeder;

/**
 * Loads the mobile carriers of each company's country into its catalog.
 * Carriers the company already has, or deleted, are left as they are.
 *
 * php artisan db:seed --class=CarrierSeeder
 */
class CarrierSeeder extends Seeder
{
    /**
     * Mobile carriers by ISO 3166-1 alpha-2 country code.
     *
     * @var array<string, list<array{name: string, website: ?string}>>
     */
    const array CARRIERS = [
        'es' => [
            ['name' => 'Movistar', 'website' => 'https://www.movistar.es'],
            ['name' => 'Orange', 'website' => 'https://www.orange.es'],
            ['name' => 'Vodafone', 'website' => 'https://www.vodafone.es'],
            ['name' => 'Digi', 'website' => 'https://www.digimobil.es'],
            ['name' => 'Yoigo', 'website' => 'https://www.yoigo.com'],
            ['name' => 'Jazztel', 'website' => 'https://www.jazztel.com'],
            ['name' => 'Simyo', 'website' => 'https://www.simyo.es'],
            ['name' => 'Pepephone', 'website' => 'https://www.pepephone.com'],
            ['name' => 'O2', 'website' => 'https://o2online.es'],
            ['name' => 'Lowi', 'website' => 'https://www.lowi.es'],
            ['name' => 'Lebara', 'website' => 'https://www.lebara.es'],
            ['name' => 'Lycamobile', 'website' => 'https://www.lycamobile.es'],
            ['name' => 'Lobster', 'website' => null],
        ],
        'ar' => [
            ['name' => 'Claro', 'website' => 'https://www.claro.com.ar'],
            ['name' => 'Personal', 'website' => 'https://www.personal.com.ar'],
            ['name' => 'Movistar', 'website' => 'https://www.movistar.com.ar'],
            ['name' => 'Tuenti', 'website' => 'https://www.tuenti.com.ar'],
        ],
        'mx' => [
            ['name' => 'Telcel', 'website' => 'https://www.telcel.com'],
            ['name' => 'AT&T', 'website' => 'https://www.att.com.mx'],
            ['name' => 'Movistar', 'website' => 'https://www.movistar.com.mx'],
        ],
        'gb' => [
            ['name' => 'EE', 'website' => 'https://ee.co.uk'],
            ['name' => 'O2', 'website' => 'https://www.o2.co.uk'],
            ['name' => 'Vodafone', 'website' => 'https://www.vodafone.co.uk'],
            ['name' => 'Three', 'website' => 'https://www.three.co.uk'],
            ['name' => '1pMobile', 'website' => null],
            ['name' => 'Lyca Mobile', 'website' => 'https://www.lycamobile.co.uk'],
            ['name' => 'spusu', 'website' => 'https://www.spusu.co.uk'],
            ['name' => 'Utility Warehouse', 'website' => 'https://www.uw.co.uk'],
            ['name' => 'giffgaff', 'website' => 'https://www.giffgaff.com'],
            ['name' => 'Sky Mobile', 'website' => 'https://www.sky.com/mobile'],
            ['name' => 'Tesco Mobile', 'website' => 'https://www.tescomobile.com'],
            ['name' => 'Asda Mobile', 'website' => 'https://www.asdamobile.co.uk'],
            ['name' => 'iD Mobile', 'website' => 'https://www.idmobile.co.uk'],
            ['name' => 'Lebara', 'website' => 'https://www.lebara.co.uk'],
            ['name' => 'SMARTY', 'website' => 'https://smarty.co.uk'],
            ['name' => 'Talkmobile', 'website' => 'https://www.talkmobile.co.uk'],
            ['name' => 'VOXI', 'website' => 'https://www.voxi.co.uk'],
        ],
        'pa' => [
            ['name' => '+Móvil', 'website' => 'https://www.masmovilpanama.com'],
            ['name' => 'Tigo', 'website' => 'https://www.tigo.com.pa'],
        ],
    ];

    public function run(): void
    {
        Company::query()
            ->whereIn('country_id', array_keys(self::CARRIERS))
            ->each(fn (Company $company) => $this->seedCompany($company));
    }

    public function seedCompany(Company $company): void
    {
        $countryId = strtolower((string) $company->country_id);
        $existing = Carrier::withTrashed()
            ->ofCompany($company->id)
            ->pluck('name')
            ->map(fn (string $name): string => mb_strtolower($name))
            ->all();

        foreach (self::CARRIERS[$countryId] ?? [] as $carrier) {
            if (in_array(mb_strtolower($carrier['name']), $existing, true)) {
                continue;
            }

            Carrier::create([...$carrier, 'company_id' => $company->id, 'country_id' => $countryId]);
        }
    }
}
