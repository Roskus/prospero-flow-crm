<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Company;

use App\Models\Company;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CompanyCreateControllerTest extends TestCase
{
    #[Test]
    public function super_admin_can_create_a_company(): void
    {
        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/company', [
                'name' => 'New Api Company',
                'currency' => 'EUR',
                'email' => 'info@example.com',
                'status' => Company::INACTIVE,
            ]);

        $response->assertCreated();
        $response->assertJsonPath('company.name', 'New Api Company');
        $this->assertDatabaseHas('company', [
            'name' => 'New Api Company',
            'currency' => 'EUR',
            'email' => 'info@example.com',
            'status' => Company::INACTIVE,
        ]);
    }

    #[Test]
    public function created_company_is_active_by_default(): void
    {
        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/company', [
                'name' => 'Default Status Company',
                'currency' => 'EUR',
            ]);

        $response->assertCreated();
        $this->assertDatabaseHas('company', ['name' => 'Default Status Company', 'status' => Company::ACTIVE]);
    }

    #[Test]
    public function non_super_admin_cannot_create_a_company_even_with_permission(): void
    {
        $companyAdmin = User::factory()->create();
        $companyAdmin->assignRole('User');
        $companyAdmin->givePermissionTo(Permission::create(['name' => 'create company', 'guard_name' => 'api']));

        $response = $this->actingAs($companyAdmin, 'api')
            ->postJson('/api/company', [
                'name' => 'Forbidden Company',
                'currency' => 'EUR',
            ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('company', ['name' => 'Forbidden Company']);
    }

    #[Test]
    public function it_rejects_invalid_data(): void
    {
        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/company', [
                'email' => 'not-an-email',
                'id' => 1,
            ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['name', 'currency', 'email']);
    }

    #[Test]
    public function it_ignores_a_body_id_and_always_creates_a_new_company(): void
    {
        $existingCompany = Company::factory()->create();

        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/company', [
                'id' => $existingCompany->id,
                'name' => 'Brand New Company',
                'currency' => 'EUR',
            ]);

        $response->assertCreated();
        $this->assertNotEquals($existingCompany->id, $response->json('company.id'));
        $this->assertNotEquals('Brand New Company', $existingCompany->fresh()->name);
    }
}
