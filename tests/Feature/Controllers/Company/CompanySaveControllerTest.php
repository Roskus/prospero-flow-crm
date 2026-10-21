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
        $companyUser = User::factory()->create(['company_id' => $company->id]);
        $company->delete();

        $response = $this->post('/company/save', [
            'id' => $company->id,
            'name' => $company->name,
            'currency' => 'USD',
            'status' => Company::ACTIVE,
        ]);

        $response->assertRedirect('/company');
        $this->assertNotSoftDeleted('company', ['id' => $company->id]);
        $this->assertNotSoftDeleted('user', ['id' => $companyUser->id]);
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

    #[Test]
    public function it_can_save_the_dashboard_calendar_view(): void
    {
        $company = Company::factory()->create();

        $response = $this->post('/company/save', [
            'id' => $company->id,
            'name' => $company->name,
            'currency' => 'USD',
            'dashboard_calendar_view' => Company::CALENDAR_VIEW_WEEK,
        ]);

        $response->assertRedirect('/company');
        $this->assertDatabaseHas('company', ['id' => $company->id, 'dashboard_calendar_view' => Company::CALENDAR_VIEW_WEEK]);
    }

    #[Test]
    public function it_rejects_an_invalid_dashboard_calendar_view(): void
    {
        $company = Company::factory()->create();

        $response = $this->post('/company/save', [
            'id' => $company->id,
            'name' => $company->name,
            'currency' => 'USD',
            'dashboard_calendar_view' => 'yearly',
        ]);

        $response->assertSessionHasErrors('dashboard_calendar_view');
    }

    #[Test]
    public function super_admin_sets_the_client_quota_and_the_parent_company_of_a_company(): void
    {
        $parentCompany = Company::factory()->create();
        $clientCompany = Company::factory()->create();

        $this->post('/company/save', ['id' => $parentCompany->id, 'name' => $parentCompany->name, 'currency' => 'USD', 'managed_companies_limit' => 5])
            ->assertRedirect('/company');
        $this->post('/company/save', ['id' => $clientCompany->id, 'name' => $clientCompany->name, 'currency' => 'USD', 'parent_company_id' => $parentCompany->id])
            ->assertRedirect('/company');

        $this->assertDatabaseHas('company', ['id' => $parentCompany->id, 'managed_companies_limit' => 5, 'parent_company_id' => null]);
        $this->assertDatabaseHas('company', ['id' => $clientCompany->id, 'parent_company_id' => $parentCompany->id]);
    }

    #[Test]
    public function a_company_cannot_be_its_own_parent_company_nor_a_client_of_a_client(): void
    {
        $parentCompany = Company::factory()->create();
        $clientCompany = Company::factory()->clientOf($parentCompany)->create();
        $other = Company::factory()->create();

        $this->post('/company/save', ['id' => $parentCompany->id, 'name' => $parentCompany->name, 'currency' => 'USD', 'parent_company_id' => $parentCompany->id])
            ->assertSessionHasErrors('parent_company_id');
        $this->post('/company/save', ['id' => $other->id, 'name' => $other->name, 'currency' => 'USD', 'parent_company_id' => $clientCompany->id])
            ->assertSessionHasErrors('parent_company_id');
        $this->post('/company/save', ['id' => $parentCompany->id, 'name' => $parentCompany->name, 'currency' => 'USD', 'parent_company_id' => $other->id])
            ->assertSessionHasErrors('parent_company_id');
    }

    #[Test]
    public function non_super_admin_cannot_change_the_quota_nor_the_parent_company(): void
    {
        $parentCompany = Company::factory()->create();
        $companyAdmin = User::factory()->create();
        $companyAdmin->givePermissionTo(Permission::create(['name' => 'update company', 'guard_name' => 'web']));

        $this->actingAs($companyAdmin)->post('/company/save', [
            'id' => $companyAdmin->company_id,
            'name' => 'Own Company',
            'currency' => 'USD',
            'managed_companies_limit' => 50,
            'parent_company_id' => $parentCompany->id,
        ])->assertSessionHasErrors(['managed_companies_limit', 'parent_company_id']);

        $this->assertDatabaseHas('company', ['id' => $companyAdmin->company_id, 'managed_companies_limit' => 0, 'parent_company_id' => null]);
    }

    #[Test]
    public function the_company_form_shows_the_quota_fields_only_to_super_admin(): void
    {
        $parentCompany = Company::factory()->withQuota(3)->create();
        Company::factory()->clientOf($parentCompany)->create();

        $this->get('/company/update/'.$parentCompany->id)
            ->assertOk()
            ->assertSee('name="managed_companies_limit"', false)
            ->assertSee('name="parent_company_id"', false)
            ->assertSee(__('Used: :used', ['used' => 1]));

        $companyAdmin = User::factory()->create();
        $companyAdmin->givePermissionTo(Permission::create(['name' => 'update company', 'guard_name' => 'web']));

        $this->actingAs($companyAdmin)->get('/company/update/'.$companyAdmin->company_id)
            ->assertOk()
            ->assertDontSee('name="managed_companies_limit"', false);
    }

    #[Test]
    public function it_saves_the_full_address(): void
    {
        $company = Company::factory()->create();

        $response = $this->post('/company/save', [
            'id' => $company->id,
            'name' => $company->name,
            'currency' => 'ARS',
            'country_id' => 'AR',
            'province' => 'Buenos Aires',
            'city' => 'CABA',
            'locality' => 'Palermo',
            'street' => 'Thames 100',
            'address_extra' => 'Piso 3 B',
            'zipcode' => 'C1414',
            'latitude' => '-34.5889',
            'longitude' => '-58.4306',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('company', [
            'id' => $company->id,
            'country_id' => 'ar',
            'locality' => 'Palermo',
            'address_extra' => 'Piso 3 B',
            'latitude' => -34.5889,
            'longitude' => -58.4306,
        ]);
        $this->assertSame('Thames 100, Piso 3 B, Palermo, C1414 CABA, Buenos Aires, Argentina', $company->fresh()->address->oneLine());
    }

    #[Test]
    public function it_rejects_a_street_longer_than_80_characters(): void
    {
        $company = Company::factory()->create();

        $response = $this->post('/company/save', [
            'id' => $company->id,
            'name' => $company->name,
            'currency' => 'EUR',
            'street' => Str::repeat('a', 81),
        ]);

        $response->assertSessionHasErrors('street');
        $this->assertNotSame(Str::repeat('a', 81), $company->fresh()->street);
    }
}
