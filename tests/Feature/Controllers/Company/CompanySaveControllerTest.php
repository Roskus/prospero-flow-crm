<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Company;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CompanySaveControllerTest extends TestCase
{
    #[Test]
    public function it_can_save_company(): void
    {
        $data = [
            'name' => fake()->word(),
            'currency' => fake()->bothify('???'),
            'logo' => UploadedFile::fake()->image('logo.jpg'),
        ];

        $response = $this->post('/company/save', $data);

        $logo = Company::all()->last()->logo;
        $data['logo'] = $logo;

        $response->assertRedirect('/company');
        $this->assertDatabaseHas('company', $data);

        $company_folder = Str::slug($data['name'], '_');
        $storagePath = 'company/'.$company_folder.'/'.$logo;

        Storage::disk('public')->assertExists($storagePath);

        Storage::disk('public')->delete($storagePath);
    }

    #[Test]
    public function super_admin_can_update_another_company(): void
    {
        $otherCompany = Company::factory()->create();

        $response = $this->post('/company/save', [
            'id' => $otherCompany->id,
            'name' => 'Updated By SuperAdmin',
            'currency' => 'USD',
        ]);

        $response->assertRedirect('/company');
        $this->assertDatabaseHas('company', ['id' => $otherCompany->id, 'name' => 'Updated By SuperAdmin']);
    }

    #[Test]
    public function it_can_save_a_deleted_company(): void
    {
        $company = Company::factory()->create();
        $company->delete();

        $response = $this->post('/company/save', [
            'id' => $company->id,
            'name' => 'Updated Deleted Company',
            'currency' => 'USD',
        ]);

        $response->assertRedirect('/company');
        $this->assertDatabaseHas('company', ['id' => $company->id, 'name' => 'Updated Deleted Company']);
        $this->assertSoftDeleted('company', ['id' => $company->id]);
    }

    #[Test]
    public function super_admin_can_change_company_status(): void
    {
        $company = Company::factory()->create(['status' => Company::ACTIVE]);

        $response = $this->post('/company/save', [
            'id' => $company->id,
            'name' => $company->name,
            'currency' => 'USD',
            'status' => Company::INACTIVE,
        ]);

        $response->assertRedirect('/company');
        $this->assertDatabaseHas('company', ['id' => $company->id, 'status' => Company::INACTIVE]);
    }

    #[Test]
    public function saving_without_status_keeps_the_current_status(): void
    {
        $company = Company::factory()->create(['status' => Company::INACTIVE]);

        $response = $this->post('/company/save', [
            'id' => $company->id,
            'name' => $company->name,
            'currency' => 'USD',
        ]);

        $response->assertRedirect('/company');
        $this->assertDatabaseHas('company', ['id' => $company->id, 'status' => Company::INACTIVE]);
    }

    #[Test]
    public function super_admin_cannot_set_an_invalid_status(): void
    {
        $company = Company::factory()->create(['status' => Company::ACTIVE]);

        $response = $this->post('/company/save', [
            'id' => $company->id,
            'name' => $company->name,
            'currency' => 'USD',
            'status' => 5,
        ]);

        $response->assertSessionHasErrors('status');
        $this->assertDatabaseHas('company', ['id' => $company->id, 'status' => Company::ACTIVE]);
    }

    #[Test]
    public function non_super_admin_cannot_change_company_status(): void
    {
        $companyAdmin = User::factory()->create();
        $companyAdmin->assignRole('User');
        $companyAdmin->givePermissionTo([
            Permission::create(['name' => 'create company', 'guard_name' => 'web']),
            Permission::create(['name' => 'update company', 'guard_name' => 'web']),
        ]);
        Company::whereKey($companyAdmin->company_id)->update(['status' => Company::ACTIVE]);

        $response = $this->actingAs($companyAdmin)->post('/company/save', [
            'id' => $companyAdmin->company_id,
            'name' => 'Own Company',
            'currency' => 'USD',
            'status' => Company::INACTIVE,
        ]);

        $response->assertSessionHasErrors('status');
        $this->assertDatabaseHas('company', ['id' => $companyAdmin->company_id, 'status' => Company::ACTIVE]);
        $this->assertDatabaseMissing('company', ['id' => $companyAdmin->company_id, 'name' => 'Own Company']);
    }

    #[Test]
    public function user_with_only_update_permission_can_save_own_company(): void
    {
        $companyAdmin = User::factory()->create();
        $companyAdmin->assignRole('User');
        $companyAdmin->givePermissionTo(Permission::create(['name' => 'update company', 'guard_name' => 'web']));

        $response = $this->actingAs($companyAdmin)->post('/company/save', [
            'id' => $companyAdmin->company_id,
            'name' => 'Own Company Updated',
            'currency' => 'USD',
        ]);

        $response->assertRedirect('/company');
        $this->assertDatabaseHas('company', ['id' => $companyAdmin->company_id, 'name' => 'Own Company Updated']);
    }

    #[Test]
    public function non_super_admin_cannot_create_a_company_even_with_permission(): void
    {
        $companyAdmin = User::factory()->create();
        $companyAdmin->assignRole('User');
        $companyAdmin->givePermissionTo([
            Permission::create(['name' => 'create company', 'guard_name' => 'web']),
            Permission::create(['name' => 'update company', 'guard_name' => 'web']),
        ]);

        $response = $this->actingAs($companyAdmin)->post('/company/save', [
            'name' => 'Forbidden Company',
            'currency' => 'USD',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('company', ['name' => 'Forbidden Company']);
    }

    #[Test]
    public function setting_status_to_active_restores_a_deleted_company(): void
    {
        $company = Company::factory()->create(['status' => Company::INACTIVE]);
        $company->delete();

        $response = $this->post('/company/save', [
            'id' => $company->id,
            'name' => $company->name,
            'currency' => 'USD',
            'status' => Company::ACTIVE,
        ]);

        $response->assertRedirect('/company');
        $this->assertNotSoftDeleted('company', ['id' => $company->id]);
        $this->assertEquals(Company::ACTIVE, $company->fresh()->status);
    }

    #[Test]
    public function keeping_status_inactive_does_not_restore_a_deleted_company(): void
    {
        $company = Company::factory()->create(['status' => Company::INACTIVE]);
        $company->delete();

        $response = $this->post('/company/save', [
            'id' => $company->id,
            'name' => $company->name,
            'currency' => 'USD',
            'status' => Company::INACTIVE,
        ]);

        $response->assertRedirect('/company');
        $this->assertSoftDeleted('company', ['id' => $company->id]);
    }
}
