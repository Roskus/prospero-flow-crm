<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Company;

use App\Models\Company;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CompanyUpdateControllerTest extends TestCase
{
    #[Test]
    public function it_blocks_cross_tenant_company_update(): void
    {
        $otherCompany = Company::factory()->create();

        $response = $this->actingAs($this->user, 'api')
            ->putJson("/api/company/{$otherCompany->id}", [
                'name' => 'PWNED',
                'country_id' => 'us',
            ]);

        $response->assertForbidden();
        $this->assertNotEquals('PWNED', $otherCompany->fresh()->name);
    }

    #[Test]
    public function it_allows_updating_own_company(): void
    {
        $company = $this->user->company;

        $response = $this->actingAs($this->user, 'api')
            ->putJson("/api/company/{$company->id}", [
                'name' => 'Updated Name',
                'country_id' => $company->country_id,
                'currency' => $company->currency,
            ]);

        $response->assertOk();
        $this->assertEquals('Updated Name', $company->fresh()->name);
    }
}
