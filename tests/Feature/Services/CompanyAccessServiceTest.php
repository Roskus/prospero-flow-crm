<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Models\Company;
use App\Models\User;
use App\Services\CompanyAccessService;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CompanyAccessServiceTest extends TestCase
{
    private CompanyAccessService $companyAccessService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->companyAccessService = app(CompanyAccessService::class);
    }

    #[Test]
    public function creating_users_leaves_the_default_company_empty(): void
    {
        $this->assertNull(User::factory()->create()->fresh()->default_company_id);
    }

    #[Test]
    public function the_first_grant_stores_the_default_company(): void
    {
        $parentCompany = Company::factory()->create();
        $clientCompany = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $parentCompany->id]);

        $this->companyAccessService->grant($user, $clientCompany, $this->user);

        $user->refresh();
        $this->assertSame($parentCompany->id, $user->default_company_id);
        $this->assertSame($parentCompany->id, $user->company_id);
        $this->assertDatabaseHas('user_company', [
            'user_id' => $user->id,
            'company_id' => $clientCompany->id,
            'granted_by' => $this->user->id,
        ]);
    }

    #[Test]
    public function further_grants_keep_the_default_company_even_while_working_in_a_client(): void
    {
        $parentCompany = Company::factory()->create();
        $firstClientCompany = Company::factory()->create();
        $secondClientCompany = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $parentCompany->id]);

        $this->companyAccessService->grant($user, $firstClientCompany);
        $user->update(['company_id' => $firstClientCompany->id]);
        $this->companyAccessService->grant($user->fresh(), $secondClientCompany);

        $this->assertSame($parentCompany->id, $user->fresh()->default_company_id);
        $this->assertSame(2, $user->extraCompanies()->count());
    }

    #[Test]
    public function granting_the_default_company_does_nothing(): void
    {
        $user = User::factory()->create();

        $this->companyAccessService->grant($user, $user->company);

        $this->assertNull($user->fresh()->default_company_id);
        $this->assertDatabaseMissing('user_company', ['user_id' => $user->id]);
    }

    #[Test]
    public function revoking_the_active_company_moves_the_user_back_to_the_default_company(): void
    {
        $parentCompany = Company::factory()->create();
        $firstClientCompany = Company::factory()->create();
        $secondClientCompany = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $parentCompany->id]);
        $this->companyAccessService->grant($user, $firstClientCompany);
        $this->companyAccessService->grant($user->fresh(), $secondClientCompany);
        $user->fresh()->update(['company_id' => $firstClientCompany->id]);

        $this->companyAccessService->revoke($user->fresh(), $firstClientCompany);

        $user->refresh();
        $this->assertSame($parentCompany->id, $user->company_id);
        $this->assertSame($parentCompany->id, $user->default_company_id);
        $this->assertFalse($user->canAccessCompany($firstClientCompany->id));
        $this->assertTrue($user->canAccessCompany($secondClientCompany->id));
    }

    #[Test]
    public function revoking_the_last_extra_company_clears_the_default_company(): void
    {
        $parentCompany = Company::factory()->create();
        $clientCompany = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $parentCompany->id]);
        $this->companyAccessService->grant($user, $clientCompany);
        $user->fresh()->update(['company_id' => $clientCompany->id]);

        $this->companyAccessService->revoke($user->fresh(), $clientCompany);

        $user->refresh();
        $this->assertSame($parentCompany->id, $user->company_id);
        $this->assertNull($user->default_company_id);
        $this->assertFalse(User::query()->multiCompany()->whereKey($user->id)->exists());
    }

    #[Test]
    public function making_the_only_extra_company_the_default_one_clears_the_multi_company_access(): void
    {
        $parentCompany = Company::factory()->create();
        $clientCompany = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $parentCompany->id]);
        $this->companyAccessService->grant($user, $clientCompany);
        $this->companyAccessService->accessibleCompanies($user->fresh());
        $user->refresh();
        $user->default_company_id = $clientCompany->id;
        $user->company_id = $clientCompany->id;
        $user->save();

        $this->companyAccessService->syncExtraCompanies($user->fresh(), []);

        $user->refresh();
        $this->assertNull($user->default_company_id);
        $this->assertSame($clientCompany->id, $user->company_id);
        $this->assertDatabaseMissing('user_company', ['user_id' => $user->id]);
        $this->assertFalse(Cache::has(CompanyAccessService::cacheKey($user->id)));
    }

    #[Test]
    public function the_default_company_cannot_be_revoked(): void
    {
        $user = User::factory()->create();

        $this->expectException(InvalidArgumentException::class);
        $this->companyAccessService->revoke($user, $user->company);
    }

    #[Test]
    public function accessible_companies_are_cached_and_forgotten_on_changes(): void
    {
        $parentCompany = Company::factory()->create(['name' => 'A Parent company']);
        $clientCompany = Company::factory()->create(['name' => 'B Client company']);
        $user = User::factory()->create(['company_id' => $parentCompany->id]);

        $this->assertTrue($this->companyAccessService->accessibleCompanies($user)->isEmpty());

        $this->companyAccessService->grant($user, $clientCompany);
        $companies = $this->companyAccessService->accessibleCompanies($user->fresh());

        $this->assertSame([$parentCompany->id, $clientCompany->id], $companies->pluck('id')->all());
        $this->assertTrue(Cache::has(CompanyAccessService::cacheKey($user->id)));

        $this->companyAccessService->revoke($user->fresh(), $clientCompany);
        $this->assertFalse(Cache::has(CompanyAccessService::cacheKey($user->id)));
    }

    #[Test]
    public function accessible_companies_list_the_default_company_first_and_the_rest_by_name(): void
    {
        $parentCompany = Company::factory()->create(['name' => 'M Parent company']);
        $firstClientCompany = Company::factory()->create(['name' => 'Z Client company']);
        $secondClientCompany = Company::factory()->create(['name' => 'A Client company']);
        $user = User::factory()->create(['company_id' => $parentCompany->id]);
        $this->companyAccessService->grant($user, $firstClientCompany);
        $this->companyAccessService->grant($user->fresh(), $secondClientCompany);

        $this->assertSame(
            [$parentCompany->id, $secondClientCompany->id, $firstClientCompany->id],
            $this->companyAccessService->accessibleCompanies($user->fresh())->pluck('id')->all()
        );
    }

    #[Test]
    public function deleted_companies_are_not_accessible_in_the_switcher(): void
    {
        $parentCompany = Company::factory()->create();
        $clientCompany = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $parentCompany->id]);
        $this->companyAccessService->grant($user, $clientCompany);

        $clientCompany->delete();
        $this->companyAccessService->forgetCompanyUsers($clientCompany);

        $this->assertSame([$parentCompany->id], $this->companyAccessService->accessibleCompanies($user->fresh())->pluck('id')->all());
    }

    #[Test]
    public function granting_a_company_without_parent_makes_it_a_client_company_of_the_default_company(): void
    {
        $parentCompany = Company::factory()->withQuota(3)->create();
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $parentCompany->id]);

        $this->companyAccessService->grant($user, $company, $this->user);

        $this->assertSame($parentCompany->id, $company->fresh()->parent_company_id);
        $this->assertSame(1, $parentCompany->usedClientQuota());
    }

    #[Test]
    public function granting_a_client_company_of_another_parent_company_keeps_its_parent(): void
    {
        $parentCompany = Company::factory()->create();
        $otherParentCompany = Company::factory()->create();
        $clientCompany = Company::factory()->clientOf($otherParentCompany)->create();
        $user = User::factory()->create(['company_id' => $parentCompany->id]);

        $this->companyAccessService->grant($user, $clientCompany);

        $this->assertSame($otherParentCompany->id, $clientCompany->fresh()->parent_company_id);
        $this->assertSame(0, $parentCompany->usedClientQuota());
    }

    #[Test]
    public function granting_a_company_with_client_companies_keeps_it_without_parent(): void
    {
        $parentCompany = Company::factory()->create();
        $otherParentCompany = Company::factory()->create();
        Company::factory()->clientOf($otherParentCompany)->create();
        $user = User::factory()->create(['company_id' => $parentCompany->id]);

        $this->companyAccessService->grant($user, $otherParentCompany);

        $this->assertNull($otherParentCompany->fresh()->parent_company_id);
    }

    #[Test]
    public function a_user_of_a_client_company_does_not_make_other_companies_its_clients(): void
    {
        $parentCompany = Company::factory()->create();
        $clientCompany = Company::factory()->clientOf($parentCompany)->create();
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $clientCompany->id]);

        $this->companyAccessService->grant($user, $company);

        $this->assertNull($company->fresh()->parent_company_id);
    }
}
