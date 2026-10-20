<?php

declare(strict_types=1);

namespace Tests\Feature\Migrations;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MultiCompanyAccessMigrationTest extends TestCase
{
    #[Test]
    public function it_creates_the_multi_company_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('user_company', ['id', 'user_id', 'company_id', 'granted_by', 'created_at', 'updated_at']));
        $this->assertTrue(Schema::hasColumns('company', ['managed_companies_limit', 'parent_company_id']));
        $this->assertTrue(Schema::hasColumn('user', 'default_company_id'));
    }

    #[Test]
    public function new_companies_and_users_get_neutral_defaults(): void
    {
        $company = Company::factory()->create()->fresh();
        $user = User::factory()->create(['company_id' => $company->id])->fresh();

        $this->assertSame(0, (int) $company->managed_companies_limit);
        $this->assertNull($company->parent_company_id);
        $this->assertNull($user->default_company_id);
        $this->assertSame(0, DB::table('user_company')->count());
    }

    #[Test]
    public function a_user_cannot_be_granted_the_same_company_twice(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();

        DB::table('user_company')->insert(['user_id' => $user->id, 'company_id' => $company->id]);

        $this->expectException(QueryException::class);
        DB::table('user_company')->insert(['user_id' => $user->id, 'company_id' => $company->id]);
    }
}
