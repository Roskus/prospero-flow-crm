<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Branch;
use App\Models\Company;
use App\Models\User;
use App\Services\BranchService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserBranchTest extends TestCase
{
    #[Test]
    public function a_new_user_gets_the_main_branch_of_their_company(): void
    {
        $company = Company::factory()->create();

        $user = User::factory()->create(['company_id' => $company->id]);

        $this->assertSame($company->mainBranch->id, $user->fresh()->default_branch_id);
        $this->assertTrue($user->currentBranch()->is($company->mainBranch));
    }

    #[Test]
    public function a_user_works_from_their_default_branch(): void
    {
        $company = Company::factory()->create();
        $branch = Branch::factory()->forCompany($company)->create();

        $user = User::factory()->create(['company_id' => $company->id, 'default_branch_id' => $branch->id]);

        $this->assertTrue($user->defaultBranch->is($branch));
        $this->assertTrue($user->currentBranch()->is($branch));
    }

    #[Test]
    public function a_deleted_default_branch_falls_back_to_the_main_branch(): void
    {
        $company = Company::factory()->create();
        $branch = Branch::factory()->forCompany($company)->create();
        $user = User::factory()->create(['company_id' => $company->id, 'default_branch_id' => $branch->id]);

        app(BranchService::class)->delete($branch);

        $user = $user->fresh();
        $this->assertSame($branch->id, $user->default_branch_id);
        $this->assertTrue($user->currentBranch()->is($company->mainBranch));
    }

    #[Test]
    public function a_multi_company_user_in_a_client_company_works_from_its_main_branch(): void
    {
        $ownCompany = Company::factory()->create();
        $clientCompany = Company::factory()->create();
        $branch = Branch::factory()->forCompany($ownCompany)->create();
        $user = User::factory()->create(['company_id' => $ownCompany->id, 'default_branch_id' => $branch->id]);
        $user->forceFill(['default_company_id' => $ownCompany->id, 'company_id' => $clientCompany->id])->saveQuietly();

        $user = $user->fresh();
        $this->assertTrue($user->currentBranch()->is($clientCompany->mainBranch));
        $this->assertSame($branch->id, $user->default_branch_id);
    }

    #[Test]
    public function changing_the_main_branch_keeps_the_default_branch_of_existing_users(): void
    {
        $company = Company::factory()->create();
        $previousMain = $company->mainBranch;
        $user = User::factory()->create(['company_id' => $company->id]);

        app(BranchService::class)->makeMain(Branch::factory()->forCompany($company)->create());

        $this->assertSame($previousMain->id, $user->fresh()->default_branch_id);
    }
}
