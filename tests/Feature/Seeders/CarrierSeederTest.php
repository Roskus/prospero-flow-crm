<?php

declare(strict_types=1);

namespace Tests\Feature\Seeders;

use App\Models\Carrier;
use App\Models\Company;
use Database\Seeders\CarrierSeeder;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CarrierSeederTest extends TestCase
{
    #[Test]
    public function it_loads_the_carriers_of_the_company_country_once(): void
    {
        $spanish = Company::factory()->create(['country_id' => 'es']);
        $argentine = Company::factory()->create(['country_id' => 'ar']);
        $mexican = Company::factory()->create(['country_id' => 'mx']);
        $british = Company::factory()->create(['country_id' => 'gb']);
        $panamanian = Company::factory()->create(['country_id' => 'pa']);
        $french = Company::factory()->create(['country_id' => 'fr']);

        $this->seed(CarrierSeeder::class);
        $this->seed(CarrierSeeder::class);

        $carriers = Carrier::ofCompany($spanish->id)->get();
        $this->assertCount(count(CarrierSeeder::CARRIERS['es']), $carriers);
        $this->assertTrue($carriers->every(fn (Carrier $carrier): bool => $carrier->country_id === 'es'));
        $this->assertTrue($carriers->contains('name', 'Movistar'));
        $this->assertEqualsCanonicalizing(['Claro', 'Personal', 'Movistar', 'Tuenti'], Carrier::ofCompany($argentine->id)->pluck('name')->all());
        $this->assertSame(['ar'], Carrier::ofCompany($argentine->id)->distinct()->pluck('country_id')->all());
        $this->assertEqualsCanonicalizing(['Telcel', 'AT&T', 'Movistar'], Carrier::ofCompany($mexican->id)->pluck('name')->all());
        $this->assertSame(count(CarrierSeeder::CARRIERS['gb']), Carrier::ofCompany($british->id)->where('country_id', 'gb')->count());
        $this->assertEqualsCanonicalizing(['+Móvil', 'Tigo'], Carrier::ofCompany($panamanian->id)->pluck('name')->all());
        $this->assertSame(0, Carrier::ofCompany($french->id)->count());
    }

    #[Test]
    public function it_keeps_the_carriers_the_company_already_has_or_deleted(): void
    {
        $company = Company::factory()->create(['country_id' => 'es']);
        Carrier::factory()->forCompany($company)->create(['name' => 'MOVISTAR', 'phone' => '1004']);
        Carrier::factory()->forCompany($company)->create(['name' => 'Vodafone'])->delete();

        (new CarrierSeeder)->seedCompany($company);

        $this->assertSame(1, Carrier::ofCompany($company->id)->whereIn('name', ['Movistar', 'MOVISTAR'])->count());
        $this->assertSame('1004', Carrier::ofCompany($company->id)->where('name', 'MOVISTAR')->value('phone'));
        $this->assertFalse(Carrier::ofCompany($company->id)->where('name', 'Vodafone')->exists());
        $this->assertSame(count(CarrierSeeder::CARRIERS['es']) - 2, Carrier::ofCompany($company->id)->whereNotIn('name', ['MOVISTAR'])->count());
    }
}
