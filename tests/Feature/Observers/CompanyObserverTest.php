<?php

declare(strict_types=1);

namespace Tests\Feature\Observers;

use App\Models\Company;
use App\Models\User;
use App\Services\CompanyAccessService;
use Illuminate\Auth\SessionGuard;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CompanyObserverTest extends TestCase
{
    #[Test]
    public function deleting_a_company_soft_deletes_its_users(): void
    {
        $company = Company::factory()->create();
        $users = User::factory()->count(2)->create(['company_id' => $company->id]);
        $otherUser = User::factory()->create();

        $company->delete();

        foreach ($users as $user) {
            $this->assertSoftDeleted('user', ['id' => $user->id]);
        }
        $this->assertNotSoftDeleted('user', ['id' => $otherUser->id]);
    }

    #[Test]
    public function restoring_a_company_restores_only_users_deleted_with_it(): void
    {
        $company = Company::factory()->create();
        $previouslyDeletedUser = User::factory()->create(['company_id' => $company->id]);
        $this->travelTo(Carbon::now()->subDay(), fn () => $previouslyDeletedUser->delete());
        $user = User::factory()->create(['company_id' => $company->id]);

        $company->delete();
        $company->restore();

        $this->assertNotSoftDeleted('user', ['id' => $user->id]);
        $this->assertSoftDeleted('user', ['id' => $previouslyDeletedUser->id]);
    }

    #[Test]
    public function users_of_a_deleted_company_cannot_log_in(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id, 'password' => bcrypt('secret-password')]);
        $company->delete();

        auth()->logout();
        $response = $this->post('/login', ['email' => $user->email, 'password' => 'secret-password']);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    #[Test]
    public function logged_in_user_of_a_deleted_company_loses_access(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);
        $this->actingAs($user);
        $company->delete();

        $this->app['auth']->forgetGuards();
        $response = $this->withSession(['login_web_'.sha1(SessionGuard::class) => $user->id])->get('/user');

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function deleting_a_client_company_moves_working_multi_company_users_back_to_their_default_company_instead_of_deleting_them(): void
    {
        $parentCompany = Company::factory()->create();
        $clientCompany = Company::factory()->clientOf($parentCompany)->create();
        $clientCompanyUser = User::factory()->create(['company_id' => $clientCompany->id]);
        $multiCompanyUser = User::factory()->create(['company_id' => $parentCompany->id]);
        app(CompanyAccessService::class)->grant($multiCompanyUser, $clientCompany);
        $multiCompanyUser->fresh()->update(['company_id' => $clientCompany->id]);

        $clientCompany->delete();

        $this->assertSoftDeleted('user', ['id' => $clientCompanyUser->id]);
        $this->assertNotSoftDeleted('user', ['id' => $multiCompanyUser->id]);
        $this->assertSame($parentCompany->id, $multiCompanyUser->fresh()->company_id);
    }

    #[Test]
    public function deleting_and_restoring_a_parent_company_includes_multi_company_users_working_in_a_client_company(): void
    {
        $parentCompany = Company::factory()->create();
        $clientCompany = Company::factory()->clientOf($parentCompany)->create();
        $multiCompanyUser = User::factory()->create(['company_id' => $parentCompany->id]);
        app(CompanyAccessService::class)->grant($multiCompanyUser, $clientCompany);
        $multiCompanyUser->fresh()->update(['company_id' => $clientCompany->id]);

        $parentCompany->delete();
        $this->assertSoftDeleted('user', ['id' => $multiCompanyUser->id]);

        $parentCompany->restore();
        $this->assertNotSoftDeleted('user', ['id' => $multiCompanyUser->id]);
    }

    #[Test]
    public function renaming_a_company_refreshes_the_company_switcher(): void
    {
        $parentCompany = Company::factory()->create();
        $clientCompany = Company::factory()->create(['name' => 'Old name']);
        $multiCompanyUser = User::factory()->create(['company_id' => $parentCompany->id]);
        $companyAccessService = app(CompanyAccessService::class);
        $companyAccessService->grant($multiCompanyUser, $clientCompany);
        $companyAccessService->accessibleCompanies($multiCompanyUser->fresh());

        $clientCompany->update(['name' => 'New name']);

        $this->assertContains('New name', $companyAccessService->accessibleCompanies($multiCompanyUser->fresh())->pluck('name')->all());
    }
}
