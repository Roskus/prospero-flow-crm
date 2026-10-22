<?php

declare(strict_types=1);

namespace Tests\Feature\Migrations;

use App\Models\Branch;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BranchMigrationTest extends TestCase
{
    #[Test]
    public function every_company_gets_one_main_branch_built_from_its_data(): void
    {
        $withAddress = Company::withoutEvents(fn () => Company::factory()->create([
            'name' => 'With address',
            'phone' => '+34910000000',
            'country_id' => 'es',
            'city' => 'Madrid',
            'street' => 'Calle Mayor 1',
        ]));
        $withoutAddress = Company::withoutEvents(fn () => Company::factory()->create([
            'country_id' => null,
            'province' => null,
            'city' => null,
            'street' => null,
            'zipcode' => null,
        ]));
        $deleted = Company::withoutEvents(function () {
            $company = Company::factory()->create();
            $company->delete();

            return $company;
        });

        $this->runMigration('2026_10_21_164111_create_main_branch_for_every_company');

        $this->assertDatabaseHas('branch', [
            'company_id' => $withAddress->id,
            'name' => 'With address',
            'code' => '0001',
            'is_main' => true,
            'phone' => '+34910000000',
            'country_id' => 'es',
            'city' => 'Madrid',
            'street' => 'Calle Mayor 1',
        ]);
        $this->assertTrue(Branch::ofCompany($withoutAddress->id)->sole()->address->isEmpty());
        $this->assertSame(1, Branch::ofCompany($deleted->id)->where('is_main', true)->count());
    }

    #[Test]
    public function running_the_branch_migration_twice_creates_no_duplicates(): void
    {
        $company = Company::withoutEvents(fn () => Company::factory()->create());

        $this->runMigration('2026_10_21_164111_create_main_branch_for_every_company');
        $this->runMigration('2026_10_21_164111_create_main_branch_for_every_company');

        $this->assertSame(1, DB::table('branch')->where('company_id', $company->id)->count());
        $this->assertSame(0, DB::table('company')
            ->whereNotExists(fn ($query) => $query->from('branch')->whereColumn('branch.company_id', 'company.id')->where('is_main', true))
            ->count());
    }

    #[Test]
    public function every_user_gets_the_main_branch_of_their_default_company(): void
    {
        $ownCompany = Company::factory()->create();
        $clientCompany = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $ownCompany->id]);
        $multiCompanyUser = User::factory()->create(['company_id' => $clientCompany->id]);
        $multiCompanyUser->forceFill(['default_company_id' => $ownCompany->id])->saveQuietly();
        User::query()->update(['default_branch_id' => null]);

        $this->runMigration('2026_10_21_164339_backfill_user_default_branch_id');

        $this->assertSame($ownCompany->mainBranch->id, $user->fresh()->default_branch_id);
        $this->assertSame($ownCompany->mainBranch->id, $multiCompanyUser->fresh()->default_branch_id);
    }

    private function runMigration(string $name): void
    {
        (require database_path("migrations/{$name}.php"))->up();
    }
}
