<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Company;

use App\Models\Company;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CompanyUpdateControllerTest extends TestCase
{
    #[Test]
    public function it_blocks_cross_tenant_company_update(): void
    {
        $otherCompany = Company::factory()->create();

        $response = $this->actingAs($this->companyAdmin(), 'api')
            ->putJson("/api/company/{$otherCompany->id}", [
                'name' => 'PWNED',
                'country_id' => 'us',
                'currency' => 'USD',
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

    #[Test]
    public function non_super_admin_cannot_change_company_status(): void
    {
        $companyAdmin = $this->companyAdmin();
        Company::whereKey($companyAdmin->company_id)->update(['status' => Company::ACTIVE]);

        $response = $this->actingAs($companyAdmin, 'api')
            ->putJson("/api/company/{$companyAdmin->company_id}", [
                'name' => 'Own Company',
                'currency' => 'USD',
                'status' => Company::INACTIVE,
            ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('status');
        $this->assertEquals(Company::ACTIVE, Company::find($companyAdmin->company_id)->status);
    }

    #[Test]
    public function super_admin_can_change_company_status(): void
    {
        $company = $this->user->company;

        $response = $this->actingAs($this->user, 'api')
            ->putJson("/api/company/{$company->id}", [
                'name' => $company->name,
                'currency' => $company->currency,
                'status' => Company::INACTIVE,
            ]);

        $response->assertOk();
        $this->assertEquals(Company::INACTIVE, $company->fresh()->status);
    }

    #[Test]
    public function it_rejects_invalid_data(): void
    {
        $company = $this->user->company;

        $response = $this->actingAs($this->user, 'api')
            ->putJson("/api/company/{$company->id}", [
                'name' => '',
                'email' => 'not-an-email',
            ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['name', 'email', 'currency']);
    }

    #[Test]
    public function it_blocks_cross_tenant_update_when_body_id_points_to_own_company(): void
    {
        $companyAdmin = $this->companyAdmin();
        $otherCompany = Company::factory()->create();

        $response = $this->actingAs($companyAdmin, 'api')
            ->putJson("/api/company/{$otherCompany->id}", [
                'id' => $companyAdmin->company_id,
                'name' => 'PWNED',
                'currency' => 'USD',
            ]);

        $response->assertForbidden();
        $this->assertNotEquals('PWNED', $otherCompany->fresh()->name);
    }

    #[Test]
    public function super_admin_can_update_another_company(): void
    {
        $otherCompany = Company::factory()->create();

        $response = $this->actingAs($this->user, 'api')
            ->putJson("/api/company/{$otherCompany->id}", [
                'name' => 'Updated By SuperAdmin',
                'currency' => 'USD',
            ]);

        $response->assertOk();
        $this->assertEquals('Updated By SuperAdmin', $otherCompany->fresh()->name);
    }

    private function companyAdmin(): User
    {
        $companyAdmin = User::factory()->create();
        $companyAdmin->assignRole('User');
        $companyAdmin->givePermissionTo(Permission::create(['name' => 'update company', 'guard_name' => 'api']));

        return $companyAdmin;
    }
}
