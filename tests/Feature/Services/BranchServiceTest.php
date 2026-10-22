<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Models\Branch;
use App\Models\Company;
use App\Models\User;
use App\Repositories\CompanyRepository;
use App\Services\BranchService;
use App\Services\CompanyAccessService;
use App\ValueObjects\Address;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BranchServiceTest extends TestCase
{
    private BranchService $branchService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branchService = app(BranchService::class);
    }

    #[Test]
    public function a_new_company_gets_one_main_branch_with_its_data(): void
    {
        $company = Company::factory()->create([
            'name' => 'Casa matriz S.A.',
            'phone' => '+34910000000',
            'email' => 'info@example.com',
            'country_id' => 'es',
            'city' => 'Madrid',
            'street' => 'Calle Mayor 1',
            'address_extra' => 'Local 2',
        ]);

        $branches = Branch::ofCompany($company->id)->get();

        $this->assertCount(1, $branches);
        $branch = $branches->first();
        $this->assertTrue($branch->is_main);
        $this->assertSame(Branch::MAIN_CODE, $branch->code);
        $this->assertSame('Casa matriz S.A.', $branch->name);
        $this->assertSame('+34910000000', $branch->phone);
        $this->assertSame('info@example.com', $branch->email);
        $this->assertSame('Calle Mayor 1, Local 2', $branch->address->lines()[0]);
        $this->assertTrue($company->mainBranch->is($branch));
    }

    #[Test]
    public function companies_created_from_the_form_the_multi_company_flow_and_the_command_get_a_main_branch(): void
    {
        $fromForm = app(CompanyRepository::class)->save(['name' => 'Form company', 'country_id' => 'es', 'currency' => 'EUR']);

        $parentCompany = Company::factory()->create(['managed_companies_limit' => 1]);
        $administrator = User::factory()->create(['company_id' => $parentCompany->id]);
        $clientCompany = app(CompanyAccessService::class)->createClientCompany($administrator, ['name' => 'Client company', 'country_id' => 'ar', 'currency' => 'ARS']);

        $this->artisan('crm:company:create')
            ->expectsQuestion('What is the name of the company?', 'Command company')
            ->expectsQuestion('What is the country ISO code of the company? (2 chars)', 'es')
            ->assertSuccessful();
        $fromCommand = Company::where('name', 'Command company')->firstOrFail();

        foreach ([$fromForm, $clientCompany, $fromCommand] as $company) {
            $this->assertSame(1, Branch::ofCompany($company->id)->where('is_main', true)->count(), $company->name);
        }
    }

    #[Test]
    public function creating_the_main_branch_twice_keeps_one(): void
    {
        $company = Company::factory()->create();

        $this->branchService->createMainBranch($company);

        $this->assertSame(1, Branch::ofCompany($company->id)->count());
    }

    #[Test]
    public function the_main_branch_is_stored_for_users_created_before_it(): void
    {
        $company = Company::withoutEvents(fn () => Company::factory()->create());
        $user = User::factory()->create(['company_id' => $company->id]);
        $this->assertNull($user->fresh()->default_branch_id);

        $branch = $this->branchService->createMainBranch($company);

        $this->assertSame($branch->id, $user->fresh()->default_branch_id);
    }

    #[Test]
    public function the_main_branch_is_not_stored_for_multi_company_users_of_another_default_company(): void
    {
        $ownCompany = Company::factory()->create();
        $clientCompany = Company::withoutEvents(fn () => Company::factory()->create());
        $user = User::factory()->create(['company_id' => $clientCompany->id]);
        $user->forceFill(['default_company_id' => $ownCompany->id, 'default_branch_id' => null])->saveQuietly();

        $this->branchService->createMainBranch($clientCompany);

        $this->assertNull($user->fresh()->default_branch_id);
    }

    #[Test]
    public function make_main_leaves_exactly_one_main_branch(): void
    {
        $company = Company::factory()->create();
        $previous = $company->mainBranch;
        $branch = Branch::factory()->forCompany($company)->create();

        $this->assertTrue($this->branchService->makeMain($branch));

        $this->assertTrue($branch->is_main);
        $this->assertFalse($previous->fresh()->is_main);
        $this->assertSame([$branch->id], Branch::ofCompany($company->id)->where('is_main', true)->pluck('id')->all());
    }

    #[Test]
    public function make_main_refuses_a_branch_deleted_before(): void
    {
        $company = Company::factory()->create();
        $main = $company->mainBranch;
        $branch = Branch::factory()->forCompany($company)->create();
        Branch::whereKey($branch->id)->delete();

        $this->assertFalse($this->branchService->makeMain($branch));

        $this->assertTrue($main->fresh()->is_main);
    }

    #[Test]
    public function delete_soft_deletes_a_branch(): void
    {
        $branch = Branch::factory()->create();

        $this->assertTrue($this->branchService->delete($branch));

        $this->assertSoftDeleted('branch', ['id' => $branch->id]);
    }

    #[Test]
    public function delete_refuses_a_branch_that_became_main(): void
    {
        $company = Company::factory()->create();
        $branch = Branch::factory()->forCompany($company)->create();
        DB::table('branch')->where('company_id', $company->id)->update(['is_main' => DB::raw('id = '.$branch->id)]);

        $this->assertFalse($this->branchService->delete($branch));

        $this->assertNotSoftDeleted('branch', ['id' => $branch->id]);
    }

    #[Test]
    public function next_code_skips_non_numeric_codes_and_counts_deleted_branches(): void
    {
        $company = Company::factory()->create();
        Branch::factory()->forCompany($company)->create(['code' => 'NORTE']);
        Branch::factory()->forCompany($company)->create(['code' => '0007'])->delete();

        $this->assertSame('0008', $this->branchService->nextCode($company));
        $this->assertSame('0002', $this->branchService->nextCode(Company::factory()->create()));
    }

    #[Test]
    public function the_main_branch_copies_an_empty_address(): void
    {
        $company = Company::withoutEvents(fn () => Company::factory()->create());
        $company->address = new Address;
        $company->saveQuietly();

        $this->assertTrue($this->branchService->createMainBranch($company)->address->isEmpty());
    }
}
