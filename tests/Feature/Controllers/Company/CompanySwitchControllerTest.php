<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Company;

use App\Models\Company;
use App\Models\User;
use App\Services\CompanyAccessService;
use App\Services\SecurityLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CompanySwitchControllerTest extends TestCase
{
    private Company $parentCompany;

    private Company $clientCompany;

    private User $multiCompanyUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->parentCompany = Company::factory()->create();
        $this->clientCompany = Company::factory()->clientOf($this->parentCompany)->create();
        $this->multiCompanyUser = User::factory()->create([
            'company_id' => $this->parentCompany->id,
            'password' => Hash::make('multi-company-password'),
        ]);
        app(CompanyAccessService::class)->grant($this->multiCompanyUser, $this->clientCompany, $this->user);
        $this->multiCompanyUser->refresh();
    }

    #[Test]
    public function a_user_can_switch_to_an_accessible_company(): void
    {
        $this->mock(SecurityLogger::class, function (MockInterface $mock) {
            $mock->shouldReceive('log')->once()->with('company_switched', [
                'from_company_id' => $this->parentCompany->id,
                'to_company_id' => $this->clientCompany->id,
            ]);
            $mock->shouldReceive('log')->once()->with('company_switched', [
                'from_company_id' => $this->clientCompany->id,
                'to_company_id' => $this->parentCompany->id,
            ]);
        });

        $this->actingAs($this->multiCompanyUser)
            ->post(route('company.switch', $this->clientCompany->id))
            ->assertRedirect('/');

        $this->assertSame($this->clientCompany->id, $this->multiCompanyUser->fresh()->company_id);

        $this->actingAs($this->multiCompanyUser->fresh())
            ->post(route('company.switch', $this->parentCompany->id))
            ->assertRedirect('/');

        $this->assertSame($this->parentCompany->id, $this->multiCompanyUser->fresh()->company_id);
    }

    #[Test]
    public function a_user_cannot_switch_to_a_company_without_access(): void
    {
        $otherCompany = Company::factory()->create();
        $this->mock(SecurityLogger::class, function (MockInterface $mock) use ($otherCompany) {
            $mock->shouldReceive('log')->once()->with('company_switch_denied', ['to_company_id' => $otherCompany->id]);
        });

        $this->actingAs($this->multiCompanyUser)
            ->post(route('company.switch', $otherCompany->id))
            ->assertForbidden();

        $this->assertSame($this->parentCompany->id, $this->multiCompanyUser->fresh()->company_id);
    }

    #[Test]
    public function a_regular_user_cannot_switch_to_another_company(): void
    {
        $seller = User::factory()->create(['company_id' => $this->parentCompany->id]);

        $this->actingAs($seller)
            ->post(route('company.switch', $this->clientCompany->id))
            ->assertForbidden();

        $this->assertSame($this->parentCompany->id, $seller->fresh()->company_id);
    }

    #[Test]
    public function a_user_cannot_switch_to_a_deleted_company(): void
    {
        $this->clientCompany->delete();

        $this->actingAs($this->multiCompanyUser)
            ->post(route('company.switch', $this->clientCompany->id))
            ->assertForbidden();

        $this->assertSame($this->parentCompany->id, $this->multiCompanyUser->fresh()->company_id);
    }

    #[Test]
    public function guests_cannot_switch_companies(): void
    {
        auth()->guard('web')->logout();

        $this->post(route('company.switch', $this->clientCompany->id))->assertRedirect(route('login'));
    }

    #[Test]
    public function web_login_starts_in_the_home_company(): void
    {
        $this->multiCompanyUser->update(['company_id' => $this->clientCompany->id]);
        $this->app['auth']->forgetGuards();

        $this->post('/login', ['email' => $this->multiCompanyUser->email, 'password' => 'multi-company-password'])
            ->assertSessionHasNoErrors();

        $this->assertSame($this->parentCompany->id, $this->multiCompanyUser->fresh()->company_id);
    }

    #[Test]
    public function api_login_keeps_the_active_company(): void
    {
        $this->multiCompanyUser->update(['company_id' => $this->clientCompany->id]);
        $this->app['auth']->forgetGuards();

        $this->postJson('/api/auth/login', ['email' => $this->multiCompanyUser->email, 'password' => 'multi-company-password'])
            ->assertOk();

        $this->assertSame($this->clientCompany->id, $this->multiCompanyUser->fresh()->company_id);
    }

    #[Test]
    public function multi_company_users_see_the_switcher_with_their_companies(): void
    {
        $response = $this->actingAs($this->multiCompanyUser)->get('/notification');

        $response->assertOk();
        $response->assertSee('id="company-switcher"', false);
        $response->assertSee('action="'.route('company.switch', $this->parentCompany->id).'"', false);
        $response->assertSee('action="'.route('company.switch', $this->clientCompany->id).'"', false);
        $response->assertSee(__('Own company'));
        $response->assertSee('aria-current="true"', false);
    }

    #[Test]
    public function single_company_users_do_not_see_the_switcher(): void
    {
        $seller = User::factory()->create(['company_id' => $this->parentCompany->id]);

        $this->actingAs($seller)->get('/notification')
            ->assertOk()
            ->assertDontSee('id="company-switcher"', false);
    }

    #[Test]
    public function single_company_users_run_no_multi_company_queries_on_the_dashboard(): void
    {
        $seller = User::factory()->create(['company_id' => $this->parentCompany->id]);

        DB::enableQueryLog();
        $this->actingAs($seller)->get('/')->assertOk();
        $queries = collect(DB::getQueryLog())->pluck('query');
        DB::disableQueryLog();

        $this->assertTrue($queries->isNotEmpty());
        $this->assertFalse($queries->contains(fn (string $query): bool => str_contains($query, 'user_company')));
        $this->assertFalse($queries->contains(fn (string $query): bool => str_contains($query, 'parent_company_id')));
    }
}
