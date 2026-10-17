<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Company;

use App\Models\Company;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CompanyRestoreControllerTest extends TestCase
{
    #[Test]
    public function super_admin_can_restore_a_deleted_company(): void
    {
        $company = Company::factory()->create(['status' => Company::INACTIVE]);
        $company->delete();

        $response = $this->patch('/company/restore/'.$company->id);

        $response->assertRedirect('/company');
        $response->assertSessionHas('success');
        $this->assertNotSoftDeleted('company', ['id' => $company->id]);
        $this->assertEquals(Company::ACTIVE, $company->fresh()->status);
    }

    #[Test]
    public function it_returns_an_error_for_a_company_that_is_not_deleted(): void
    {
        $company = Company::factory()->create();

        $response = $this->patch('/company/restore/'.$company->id);

        $response->assertRedirect('/company');
        $response->assertSessionHas('error');
    }

    #[Test]
    public function non_super_admin_cannot_restore_a_company(): void
    {
        $companyAdmin = User::factory()->create();
        $companyAdmin->assignRole('User');
        $companyAdmin->givePermissionTo(Permission::create(['name' => 'delete company', 'guard_name' => 'web']));

        $company = Company::factory()->create();
        $company->delete();

        $response = $this->actingAs($companyAdmin)->patch('/company/restore/'.$company->id);

        $response->assertForbidden();
        $this->assertSoftDeleted('company', ['id' => $company->id]);
    }

    #[Test]
    public function index_shows_restore_button_only_for_deleted_companies(): void
    {
        $activeCompany = Company::factory()->create();
        $deletedCompany = Company::factory()->create();
        $deletedCompany->delete();

        $response = $this->get('/company');

        $response->assertOk();
        $response->assertSee('/company/restore/'.$deletedCompany->id, false);
        $response->assertDontSee('/company/restore/'.$activeCompany->id, false);
    }
}
