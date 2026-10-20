<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\User;

use App\Models\Company;
use App\Models\User;
use App\Services\CompanyAccessService;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserSaveControllerTest extends TestCase
{
    #[Test]
    public function it_can_save_a_user()
    {
        $response = $this->post('user/save', []);
        $response->assertSessionHasErrors(['first_name', 'email', 'lang']);

        $data = [
            'first_name' => fake()->name(),
            'last_name' => fake()->name(),
            'email' => fake()->email(),
            'lang' => 'en',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'company_id' => $this->user->company_id,
        ];
        $response = $this->post('user/save', $data);
        $response->assertRedirect('/user');

        $response = $this->get('/user');
        $response->assertSee($data['first_name']);
        $response->assertSee($data['last_name']);
        $response->assertSee($data['email']);
    }

    #[Test]
    public function super_admin_can_update_a_user_of_another_company(): void
    {
        $otherCompanyUser = User::factory()->create();

        $response = $this->post('/user/save', [
            'id' => $otherCompanyUser->id,
            'first_name' => 'Updated',
            'last_name' => $otherCompanyUser->last_name,
            'email' => $otherCompanyUser->email,
            'lang' => 'es',
            'company_id' => $otherCompanyUser->company_id,
        ]);

        $response->assertRedirect('/user');
        $this->assertDatabaseHas('user', [
            'id' => $otherCompanyUser->id,
            'first_name' => 'Updated',
            'company_id' => $otherCompanyUser->company_id,
        ]);
    }

    #[Test]
    public function it_requires_a_password_when_creating_a_user(): void
    {
        $response = $this->from('/user/create')->post('/user/save', [
            'first_name' => 'No',
            'last_name' => 'Password',
            'email' => 'no.password@example.com',
            'lang' => 'es',
        ]);

        $response->assertSessionHasErrors(['password' => __('The password is required for new users.')]);
        $this->assertDatabaseMissing('user', ['email' => 'no.password@example.com']);
    }

    #[Test]
    public function it_rejects_a_password_conparent_companyation_that_does_not_match(): void
    {
        $response = $this->from('/user/create')->post('/user/save', [
            'first_name' => 'Wrong',
            'last_name' => 'Confirmation',
            'email' => 'wrong.confirmation@example.com',
            'lang' => 'es',
            'password' => 'initial-password',
            'password_confirmation' => 'another-password',
        ]);

        $response->assertSessionHasErrors(['password' => __('The password confirmation does not match.')]);
    }

    #[Test]
    public function super_admin_created_owner_of_a_new_company_can_log_in(): void
    {
        $company = Company::factory()->create();
        Role::findOrCreate('CompanyAdmin', 'web');

        $this->post('/user/save', [
            'first_name' => 'New',
            'last_name' => 'Owner',
            'email' => 'new.owner@example.com',
            'lang' => 'es',
            'password' => 'Owner-pass-123',
            'password_confirmation' => 'Owner-pass-123',
            'company_id' => $company->id,
            'roles' => ['CompanyAdmin'],
        ])->assertRedirect('/user');

        $owner = User::query()->where('email', 'new.owner@example.com')->firstOrFail();
        $this->assertSame($company->id, $owner->company_id);
        $this->assertTrue($owner->active);
        $this->assertTrue($owner->hasRole('CompanyAdmin'));

        $this->app['auth']->forgetGuards();
        $this->post('/login', ['email' => 'new.owner@example.com', 'password' => 'Owner-pass-123'])
            ->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($owner);
    }

    #[Test]
    public function it_keeps_the_entered_data_when_the_password_conparent_companyation_fails(): void
    {
        $company = Company::factory()->create();
        Role::findOrCreate('CompanyAdmin', 'web');

        $response = $this->from('/user/create')->followingRedirects()->post('/user/save', [
            'first_name' => 'Kept',
            'last_name' => 'Values',
            'email' => 'kept.values@example.com',
            'phone' => '600111222',
            'lang' => 'es',
            'password' => 'Owner-pass-123',
            'password_confirmation' => 'Different-123',
            'company_id' => $company->id,
            'roles' => ['CompanyAdmin'],
        ]);

        $response->assertOk();
        $response->assertSee(__('The password confirmation does not match.'));
        $response->assertSee('value="Kept"', false);
        $response->assertSee('value="Values"', false);
        $response->assertSee('value="kept.values@example.com"', false);
        $response->assertSee('value="600111222"', false);
        $response->assertSee('<option value="es" selected>', false);
        $response->assertSee('<option value="'.$company->id.'" selected>', false);
        $response->assertSee('<option value="CompanyAdmin" selected>', false);
        $response->assertDontSee('Owner-pass-123');
        $response->assertSee('autocomplete="new-password"', false);
        $this->assertDatabaseMissing('user', ['email' => 'kept.values@example.com']);
    }

    #[Test]
    public function super_admin_grants_and_revokes_other_companies_from_the_user_form(): void
    {
        $parentCompany = Company::factory()->create();
        $clientCompany = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $parentCompany->id]);
        $payload = [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'lang' => 'es',
            'company_id' => $parentCompany->id,
        ];

        $this->post('/user/save', $payload + ['extra_companies' => ['', $clientCompany->id]])->assertRedirect('/user');

        $user->refresh();
        $this->assertSame($parentCompany->id, $user->default_company_id);
        $this->assertDatabaseHas('user_company', ['user_id' => $user->id, 'company_id' => $clientCompany->id, 'granted_by' => $this->user->id]);

        $this->post('/user/save', $payload + ['extra_companies' => ['']])->assertRedirect('/user');

        $user->refresh();
        $this->assertNull($user->default_company_id);
        $this->assertDatabaseMissing('user_company', ['user_id' => $user->id]);
    }

    #[Test]
    public function editing_a_multi_company_user_keeps_their_active_company(): void
    {
        $parentCompany = Company::factory()->create();
        $clientCompany = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $parentCompany->id]);
        app(CompanyAccessService::class)->grant($user, $clientCompany);
        $user->refresh()->update(['company_id' => $clientCompany->id]);

        $this->post('/user/save', [
            'id' => $user->id,
            'first_name' => 'Renamed',
            'last_name' => $user->last_name,
            'email' => $user->email,
            'lang' => 'es',
            'company_id' => $parentCompany->id,
            'extra_companies' => ['', $clientCompany->id],
        ])->assertRedirect('/user');

        $user->refresh();
        $this->assertSame('Renamed', $user->first_name);
        $this->assertSame($clientCompany->id, $user->company_id);
        $this->assertSame($parentCompany->id, $user->default_company_id);
    }

    #[Test]
    public function changing_the_default_company_to_an_extra_one_removes_it_from_the_extras(): void
    {
        $parentCompany = Company::factory()->create();
        $clientCompany = Company::factory()->create();
        $other = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $parentCompany->id]);
        app(CompanyAccessService::class)->grant($user, $clientCompany);

        $this->post('/user/save', [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'lang' => 'es',
            'company_id' => $clientCompany->id,
            'extra_companies' => ['', $clientCompany->id, $other->id],
        ])->assertRedirect('/user');

        $user->refresh();
        $this->assertSame($clientCompany->id, $user->default_company_id);
        $this->assertSame($clientCompany->id, $user->company_id);
        $this->assertSame([$other->id], $user->extraCompanies()->pluck('company.id')->map(fn ($id) => (int) $id)->all());
    }

    #[Test]
    public function only_super_admin_can_change_the_companies_of_a_user(): void
    {
        $clientCompany = Company::factory()->create();
        $companyAdmin = User::factory()->create();
        $companyAdmin->assignRole(Role::findOrCreate('CompanyAdmin', 'web'));
        $companyAdmin->givePermissionTo(Permission::findOrCreate('update user', 'web'));
        $employee = User::factory()->create(['company_id' => $companyAdmin->company_id]);

        $this->actingAs($companyAdmin)->post('/user/save', [
            'id' => $employee->id,
            'first_name' => $employee->first_name,
            'last_name' => $employee->last_name,
            'email' => $employee->email,
            'lang' => 'es',
            'extra_companies' => [$clientCompany->id],
        ])->assertSessionHasErrors('extra_companies');

        $this->assertDatabaseMissing('user_company', ['user_id' => $employee->id]);
    }

    #[Test]
    public function the_user_form_shows_the_extra_companies_only_to_super_admin(): void
    {
        $user = User::factory()->create();

        $this->get('/user/update/'.$user->id)->assertOk()->assertSee('name="extra_companies[]"', false);
    }
}
