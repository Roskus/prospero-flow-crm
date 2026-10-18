<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Models\Brand;
use App\Models\Company;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Regression for the "Broken Access Control in the REST API" report, using the real role/permission
 * matrix from PermissionSeeder instead of hand-made permissions.
 */
class ApiBrokenAccessControlReportTest extends TestCase
{
    private User $seller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([RoleSeeder::class, PermissionSeeder::class]);

        $this->seller = User::factory()->create(['password' => Hash::make('seller-password')]);
        $this->seller->assignRole('Seller');
    }

    /**
     * Endpoints from the report that the Seller role is not granted.
     *
     * @return array<string, array{string, string}>
     */
    public static function endpointsDeniedToSeller(): array
    {
        return [
            'GET /api/user' => ['GET', '/api/user'],
            'GET /api/user/{id}' => ['GET', '/api/user/{user}'],
            'GET /api/transaction' => ['GET', '/api/transaction'],
            'GET /api/supplier' => ['GET', '/api/supplier'],
            'GET /api/supplier/{id}' => ['GET', '/api/supplier/{supplier}'],
            'GET /api/ticket' => ['GET', '/api/ticket'],
            'GET /api/ticket/{id}' => ['GET', '/api/ticket/{ticket}'],
            'DELETE /api/user/{id}' => ['DELETE', '/api/user/{user}'],
            'DELETE /api/product/{id}' => ['DELETE', '/api/product/{product}'],
            'DELETE /api/brand/{id}' => ['DELETE', '/api/brand/{brand}'],
            'POST /api/ticket' => ['POST', '/api/ticket'],
            'POST /api/supplier' => ['POST', '/api/supplier'],
            'PUT /api/supplier/{id}' => ['PUT', '/api/supplier/{supplier}'],
        ];
    }

    #[Test]
    #[DataProvider('endpointsDeniedToSeller')]
    public function seller_is_forbidden_on_endpoints_listed_in_the_report(string $method, string $uri): void
    {
        $response = $this->actingAs($this->seller, 'api')->json($method, $this->resolveUri($uri));

        $response->assertForbidden();
    }

    #[Test]
    public function proof_of_concept_with_a_real_seller_token_is_blocked(): void
    {
        $this->app['auth']->forgetGuards();
        Transaction::factory()->create(['company_id' => $this->seller->company_id]);
        $victim = User::factory()->create(['company_id' => $this->seller->company_id]);

        $token = $this->postJson('/api/auth/login', ['email' => $this->seller->email, 'password' => 'seller-password'])
            ->assertOk()
            ->json('access_token');
        $headers = ['Authorization' => 'Bearer '.$token];

        $this->getJson('/api/user', $headers)->assertForbidden()->assertDontSee($victim->email);
        $this->getJson('/api/transaction', $headers)->assertForbidden();
        $this->deleteJson('/api/user/'.$victim->id, [], $headers)->assertForbidden();

        $this->assertNotSoftDeleted('user', ['id' => $victim->id]);
    }

    #[Test]
    public function default_company_seller_cannot_delete_a_super_admin_of_another_company(): void
    {
        $defaultCompany = Company::find(Company::DEFAULT_COMPANY) ?? Company::factory()->create(['id' => Company::DEFAULT_COMPANY]);
        $this->seller->update(['company_id' => $defaultCompany->id]);
        $victim = User::factory()->create();
        $victim->assignRole('SuperAdmin');

        $response = $this->actingAs($this->seller, 'api')->deleteJson('/api/user/'.$victim->id);

        $response->assertForbidden();
        $this->assertNotSoftDeleted('user', ['id' => $victim->id]);
    }

    #[Test]
    public function default_company_admin_cannot_delete_users_of_another_company(): void
    {
        $defaultCompany = Company::find(Company::DEFAULT_COMPANY) ?? Company::factory()->create(['id' => Company::DEFAULT_COMPANY]);
        $companyAdmin = User::factory()->create(['company_id' => $defaultCompany->id]);
        $companyAdmin->assignRole('CompanyAdmin');
        $victim = User::factory()->create();

        $response = $this->actingAs($companyAdmin, 'api')->deleteJson('/api/user/'.$victim->id);

        $response->assertNotFound();
        $this->assertNotSoftDeleted('user', ['id' => $victim->id]);
    }

    /**
     * The Seller role is granted these by PermissionSeeder, so a 200 is the expected behaviour.
     *
     * @return array<string, array{string}>
     */
    public static function readsGrantedToSeller(): array
    {
        return [
            'customers' => ['/api/customer'],
            'leads' => ['/api/lead'],
            'orders' => ['/api/order'],
            'products' => ['/api/product'],
            'brands' => ['/api/brand'],
            'contacts' => ['/api/contact'],
            'calendar' => ['/api/calendar'],
        ];
    }

    #[Test]
    #[DataProvider('readsGrantedToSeller')]
    public function seller_keeps_access_to_what_its_role_grants(string $uri): void
    {
        $this->actingAs($this->seller, 'api')->getJson($uri)->assertOk();
    }

    private function resolveUri(string $uri): string
    {
        $companyId = $this->seller->company_id;

        return strtr($uri, [
            '{user}' => (string) User::factory()->create(['company_id' => $companyId])->id,
            '{supplier}' => (string) Supplier::factory()->create(['company_id' => $companyId])->id,
            '{ticket}' => (string) Ticket::factory()->create(['company_id' => $companyId])->id,
            '{product}' => (string) Product::factory()->create(['company_id' => $companyId, 'photo' => null])->id,
            '{brand}' => (string) Brand::factory()->create(['company_id' => $companyId])->id,
        ]);
    }
}
