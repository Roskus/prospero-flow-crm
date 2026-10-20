<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Company;

use App\Models\Company;
use App\Models\Lead;
use App\Models\User;
use App\Services\CompanyAccessService;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ClientCompanyCreateTest extends TestCase
{
    private Company $parentCompany;

    private User $multiCompanyAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([RoleSeeder::class, PermissionSeeder::class]);

        $this->parentCompany = Company::factory()->withQuota(2)->create();
        $this->multiCompanyAdmin = User::factory()->create(['company_id' => $this->parentCompany->id]);
        $this->multiCompanyAdmin->assignRole(User::MULTI_COMPANY_ADMIN);
    }

    #[Test]
    public function seeders_are_idempotent_and_multi_company_admin_inherits_every_company_admin_permission(): void
    {
        $this->seed([RoleSeeder::class, PermissionSeeder::class]);

        $companyAdminPermissions = Role::findByName('CompanyAdmin')->permissions->pluck('name');
        $multiCompanyAdminPermissions = Role::findByName(User::MULTI_COMPANY_ADMIN)->permissions->pluck('name');

        $this->assertSame(1, Role::query()->where('name', User::MULTI_COMPANY_ADMIN)->count());
        $this->assertTrue($companyAdminPermissions->isNotEmpty());
        $this->assertSame([], $companyAdminPermissions->diff($multiCompanyAdminPermissions)->values()->all());
        $this->assertSame(['create company'], $multiCompanyAdminPermissions->diff($companyAdminPermissions)->values()->all());
    }

    #[Test]
    public function a_multi_company_admin_alone_is_a_company_admin(): void
    {
        $this->assertSame([User::MULTI_COMPANY_ADMIN], $this->multiCompanyAdmin->getRoleNames()->all());
        $this->assertTrue($this->multiCompanyAdmin->isCompanyAdmin());
        $this->assertTrue($this->multiCompanyAdmin->can('update company'));
        $this->assertTrue($this->multiCompanyAdmin->can('read lead'));
        $this->assertTrue($this->multiCompanyAdmin->can('create company'));
    }

    #[Test]
    public function a_multi_company_admin_sees_every_lead_of_the_company_like_a_company_admin(): void
    {
        $otherSellerLead = Lead::factory()->create(['company_id' => $this->parentCompany->id, 'name' => 'Lead of another seller']);

        $this->actingAs($this->multiCompanyAdmin)->get('/lead')
            ->assertOk()
            ->assertSee($otherSellerLead->name);
        $this->actingAs($this->multiCompanyAdmin)->get('/')
            ->assertOk();
    }

    #[Test]
    public function only_super_admin_can_assign_the_multi_company_admin_role(): void
    {
        $companyAdmin = $this->createCompanyAdmin();
        $employee = User::factory()->create(['company_id' => $this->parentCompany->id]);

        $this->assertNotContains(User::MULTI_COMPANY_ADMIN, $companyAdmin->assignableRoles()->pluck('name')->all());
        $this->assertContains(User::MULTI_COMPANY_ADMIN, $this->user->assignableRoles()->pluck('name')->all());

        $this->actingAs($companyAdmin)->post('/user/save', [
            'id' => $employee->id,
            'first_name' => $employee->first_name,
            'last_name' => $employee->last_name,
            'email' => $employee->email,
            'lang' => 'es',
            'roles' => [User::MULTI_COMPANY_ADMIN],
        ])->assertSessionHasErrors('roles.0');

        $this->assertFalse($employee->fresh()->hasRole(User::MULTI_COMPANY_ADMIN));
    }

    #[Test]
    public function a_multi_company_admin_edits_their_own_data_without_touching_their_roles(): void
    {
        $this->actingAs($this->multiCompanyAdmin)->get('/user/update/'.$this->multiCompanyAdmin->id)
            ->assertOk()
            ->assertDontSee('name="roles[]"', false);

        $this->actingAs($this->multiCompanyAdmin)->post('/user/save', [
            'id' => $this->multiCompanyAdmin->id,
            'first_name' => 'Renamed',
            'last_name' => $this->multiCompanyAdmin->last_name,
            'email' => $this->multiCompanyAdmin->email,
            'lang' => 'es',
            'roles' => ['Seller'],
        ])->assertRedirect('/user');

        $this->multiCompanyAdmin->refresh();
        $this->assertSame('Renamed', $this->multiCompanyAdmin->first_name);
        $this->assertSame([User::MULTI_COMPANY_ADMIN], $this->multiCompanyAdmin->getRoleNames()->all());
    }

    #[Test]
    public function a_multi_company_admin_edits_their_own_user_while_working_in_a_client_company(): void
    {
        $clientCompany = Company::factory()->clientOf($this->parentCompany)->create();
        app(CompanyAccessService::class)->grant($this->multiCompanyAdmin, $clientCompany);
        $this->multiCompanyAdmin->refresh()->update(['company_id' => $clientCompany->id]);

        $this->actingAs($this->multiCompanyAdmin->fresh())->get('/user/update/'.$this->multiCompanyAdmin->id)->assertOk();
    }

    #[Test]
    public function editing_a_user_keeps_the_roles_the_editor_cannot_assign(): void
    {
        $companyAdmin = $this->createCompanyAdmin();
        $employee = User::factory()->create(['company_id' => $this->parentCompany->id]);
        $employee->assignRole(['Seller', 'SuperAdmin']);

        $this->assertSame(['Buyer', 'SuperAdmin'], collect($companyAdmin->rolesToSync($employee, ['Buyer']))->sort()->values()->all());
    }

    #[Test]
    public function a_company_admin_cannot_delete_a_multi_company_admin(): void
    {
        $companyAdmin = $this->createCompanyAdmin();
        $companyAdmin->givePermissionTo('delete user');

        $this->actingAs($companyAdmin)->delete('/user/delete/'.$this->multiCompanyAdmin->id)->assertForbidden();

        $this->assertNotSoftDeleted('user', ['id' => $this->multiCompanyAdmin->id]);
    }

    #[Test]
    public function a_company_admin_cannot_manage_a_multi_company_admin(): void
    {
        $this->assertFalse($this->createCompanyAdmin()->canManageUser($this->multiCompanyAdmin));
        $this->assertTrue($this->user->canManageUser($this->multiCompanyAdmin));
    }

    #[Test]
    public function a_multi_company_admin_creates_a_client_company_and_gets_access_to_it(): void
    {
        $this->actingAs($this->multiCompanyAdmin)->get('/company/create')->assertOk();

        $this->actingAs($this->multiCompanyAdmin)->post('/company/save', ['name' => 'New client company', 'currency' => 'USD'])
            ->assertRedirect(route('company.index'));

        $clientCompany = Company::query()->where('name', 'New client company')->firstOrFail();
        $this->multiCompanyAdmin->refresh();

        $this->assertSame($this->parentCompany->id, $clientCompany->parent_company_id);
        $this->assertSame($this->parentCompany->id, $this->multiCompanyAdmin->default_company_id);
        $this->assertSame($this->parentCompany->id, $this->multiCompanyAdmin->company_id);
        $this->assertTrue($this->multiCompanyAdmin->canAccessCompany($clientCompany->id));
        $this->assertDatabaseHas('user_company', [
            'user_id' => $this->multiCompanyAdmin->id,
            'company_id' => $clientCompany->id,
            'granted_by' => $this->multiCompanyAdmin->id,
        ]);
        $this->assertSame(1, $this->parentCompany->availableClientQuota());

        $this->actingAs($this->multiCompanyAdmin)->post(route('company.switch', $clientCompany->id))->assertRedirect('/');
        $this->assertSame($clientCompany->id, $this->multiCompanyAdmin->fresh()->company_id);
    }

    #[Test]
    public function client_companies_created_while_working_in_a_client_company_belong_to_the_default_company(): void
    {
        $clientCompany = Company::factory()->clientOf($this->parentCompany)->create();
        app(CompanyAccessService::class)->grant($this->multiCompanyAdmin, $clientCompany);
        $this->multiCompanyAdmin->refresh()->update(['company_id' => $clientCompany->id]);

        $this->actingAs($this->multiCompanyAdmin->fresh())->post('/company/save', ['name' => 'Second client company', 'currency' => 'USD'])
            ->assertRedirect(route('company.index'));

        $this->assertDatabaseHas('company', ['name' => 'Second client company', 'parent_company_id' => $this->parentCompany->id]);
        $this->assertSame(0, $this->parentCompany->availableClientQuota());
    }

    #[Test]
    public function a_multi_company_admin_without_quota_cannot_create_companies(): void
    {
        Company::factory()->clientOf($this->parentCompany)->count(2)->create();

        $this->actingAs($this->multiCompanyAdmin)->get('/company/create')->assertForbidden();
        $this->actingAs($this->multiCompanyAdmin)->post('/company/save', ['name' => 'Over quota company', 'currency' => 'USD'])
            ->assertForbidden();

        $this->assertDatabaseMissing('company', ['name' => 'Over quota company']);
    }

    #[Test]
    public function a_plain_company_admin_with_quota_cannot_create_companies(): void
    {
        $companyAdmin = $this->createCompanyAdmin();

        $this->actingAs($companyAdmin)->post('/company/save', ['name' => 'Not allowed company', 'currency' => 'USD'])
            ->assertForbidden();

        $this->assertDatabaseMissing('company', ['name' => 'Not allowed company']);
    }

    #[Test]
    public function a_seller_cannot_create_companies(): void
    {
        $seller = User::factory()->create(['company_id' => $this->parentCompany->id]);
        $seller->assignRole('Seller');

        $this->actingAs($seller)->post('/company/save', ['name' => 'Seller company', 'currency' => 'USD'])
            ->assertForbidden();
    }

    #[Test]
    public function super_admin_is_not_limited_by_any_quota(): void
    {
        $this->post('/company/save', ['name' => 'Super admin company', 'currency' => 'USD'])
            ->assertRedirect(route('company.index'));

        $this->assertDatabaseHas('company', ['name' => 'Super admin company', 'parent_company_id' => null]);
    }

    #[Test]
    public function the_company_list_shows_the_new_button_and_the_available_quota(): void
    {
        $this->actingAs($this->multiCompanyAdmin)->get('/company')
            ->assertOk()
            ->assertSee('href="'.url('/company/create').'"', false)
            ->assertSee(__('Available companies: :available', ['available' => 2]));

        Company::factory()->clientOf($this->parentCompany)->count(2)->create();

        $this->actingAs($this->multiCompanyAdmin)->get('/company')
            ->assertOk()
            ->assertDontSee('href="'.url('/company/create').'"', false)
            ->assertSee(__('Available companies: :available', ['available' => 0]));

        $this->actingAs($this->createCompanyAdmin())->get('/company')
            ->assertOk()
            ->assertDontSee(__('Available companies: :available', ['available' => 0]));
    }

    private function createCompanyAdmin(): User
    {
        $companyAdmin = User::factory()->create(['company_id' => $this->parentCompany->id]);
        $companyAdmin->assignRole('CompanyAdmin');

        return $companyAdmin;
    }
}
