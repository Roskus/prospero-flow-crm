<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Company;

use App\Models\Company;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CompanyDeleteControllerTest extends TestCase
{
    #[Test]
    public function it_blocks_non_superadmin_from_deleting_company(): void
    {
        $role = Role::findOrCreate('Seller', 'web');
        $seller = User::factory()->create();
        $seller->assignRole($role);

        $company = Company::factory()->create();

        $response = $this->actingAs($seller, 'api')
            ->deleteJson("/api/company/{$company->id}");

        $response->assertForbidden();
        $this->assertNotNull(Company::find($company->id));
    }

    #[Test]
    public function it_allows_superadmin_to_delete_company(): void
    {
        // $this->user has the SuperAdmin role by default from TestCase::setUp()
        $company = Company::factory()->create();

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson("/api/company/{$company->id}");

        $response->assertOk();
        $response->assertJsonFragment(['message' => 'Company deleted successfully']);
        $this->assertNull(Company::find($company->id));
    }
}
