<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MultiCompanyAccessTest extends TestCase
{
    #[Test]
    public function a_regular_user_resolves_to_their_company_without_querying_the_pivot(): void
    {
        $company = Company::factory()->create();
        $otherCompany = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id])->fresh();

        DB::enableQueryLog();
        $this->assertSame($company->id, $user->defaultCompanyId());
        $this->assertFalse($user->hasMultiCompanyAccess());
        $this->assertTrue($user->canAccessCompany($company->id));
        $this->assertFalse($user->canAccessCompany($otherCompany->id));
        $this->assertSame([], DB::getQueryLog());

        $this->assertFalse(User::query()->multiCompany()->whereKey($user->id)->exists());
    }

    #[Test]
    public function a_multi_company_user_resolves_default_and_extra_companies(): void
    {
        $parentCompany = Company::factory()->create();
        $clientCompany = Company::factory()->clientOf($parentCompany)->create();
        $notGranted = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $clientCompany->id, 'default_company_id' => $parentCompany->id]);
        $user->extraCompanies()->attach($clientCompany->id);

        $this->assertSame($parentCompany->id, $user->defaultCompanyId());
        $this->assertTrue($user->canAccessCompany($parentCompany->id));
        $this->assertTrue($user->canAccessCompany($clientCompany->id));
        $this->assertFalse($user->canAccessCompany($notGranted->id));
        $this->assertTrue(User::query()->multiCompany()->whereKey($user->id)->exists());
        $this->assertTrue($clientCompany->grantedUsers()->whereKey($user->id)->exists());
        $this->assertTrue($clientCompany->parent->is($parentCompany));
    }

    #[Test]
    public function the_client_quota_counts_deleted_client_companies_and_never_goes_below_zero(): void
    {
        $parentCompany = Company::factory()->create()->fresh();
        DB::enableQueryLog();
        $this->assertSame(0, $parentCompany->availableClientQuota());
        $this->assertSame([], DB::getQueryLog());
        DB::disableQueryLog();

        $parentCompany->update(['managed_companies_limit' => 3]);
        $clients = Company::factory()->clientOf($parentCompany)->count(2)->create();
        $this->assertSame(2, $parentCompany->usedClientQuota());
        $this->assertSame(1, $parentCompany->availableClientQuota());

        $clients->first()->delete();
        $this->assertSame(2, $parentCompany->usedClientQuota());

        Company::factory()->clientOf($parentCompany)->create();
        $this->assertSame(0, $parentCompany->availableClientQuota());

        $parentCompany->update(['managed_companies_limit' => 1]);
        $this->assertSame(0, $parentCompany->availableClientQuota());
    }

    #[Test]
    public function only_super_admins_and_multi_company_admins_with_quota_can_create_companies(): void
    {
        Role::findOrCreate('CompanyAdmin', 'web');
        Role::findOrCreate(User::MULTI_COMPANY_ADMIN, 'web');
        $parentCompany = Company::factory()->withQuota(1)->create();

        $multiCompanyAdmin = User::factory()->create(['company_id' => $parentCompany->id]);
        $multiCompanyAdmin->assignRole(['CompanyAdmin', User::MULTI_COMPANY_ADMIN]);
        $companyAdmin = User::factory()->create(['company_id' => $parentCompany->id]);
        $companyAdmin->assignRole('CompanyAdmin');
        $seller = User::factory()->create(['company_id' => $parentCompany->id]);

        $this->assertTrue($this->user->canCreateCompany());
        $this->assertTrue($multiCompanyAdmin->canCreateCompany());
        $this->assertFalse($companyAdmin->canCreateCompany());
        $this->assertFalse($seller->canCreateCompany());

        Company::factory()->clientOf($parentCompany)->create();
        $this->assertFalse($multiCompanyAdmin->canCreateCompany());
        $this->assertTrue($this->user->canCreateCompany());
    }

    #[Test]
    public function the_quota_is_checked_on_the_default_company_while_working_in_a_client_company(): void
    {
        Role::findOrCreate(User::MULTI_COMPANY_ADMIN, 'web');
        $parentCompany = Company::factory()->withQuota(2)->create();
        $clientCompany = Company::factory()->clientOf($parentCompany)->create();
        $multiCompanyUser = User::factory()->create(['company_id' => $clientCompany->id, 'default_company_id' => $parentCompany->id]);
        $multiCompanyUser->assignRole(User::MULTI_COMPANY_ADMIN);

        $this->assertTrue($multiCompanyUser->canCreateCompany());

        Company::factory()->clientOf($parentCompany)->create();
        $this->assertFalse($multiCompanyUser->canCreateCompany());
    }
}
