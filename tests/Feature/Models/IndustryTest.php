<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Company;
use App\Models\Industry;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class IndustryTest extends TestCase
{
    #[Test]
    public function a_company_with_its_own_industries_only_gets_those(): void
    {
        $company = Company::factory()->create();
        $companyIndustry = Industry::factory()->create(['company_id' => $company->id]);
        Industry::factory()->create(['company_id' => null]);

        $industries = (new Industry)->getForCompany($company->id);

        $this->assertSame([$companyIndustry->id], $industries->pluck('id')->all());
    }

    #[Test]
    public function a_company_without_its_own_industries_gets_the_global_ones(): void
    {
        Cache::flush();
        $company = Company::factory()->create();
        $globalIndustry = Industry::factory()->create(['company_id' => null]);
        Industry::factory()->create(['company_id' => Company::factory()->create()->id]);

        $industries = (new Industry)->getForCompany($company->id);

        $this->assertSame([$globalIndustry->id], $industries->pluck('id')->all());
    }
}
