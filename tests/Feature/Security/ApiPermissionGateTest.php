<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Models\Product;
use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ApiPermissionGateTest extends TestCase
{
    /**
     * @return array<string, array{string, string}>
     */
    public static function endpointsWithoutPermission(): array
    {
        return [
            'delete product' => ['DELETE', '/api/product/1'],
            'create brand' => ['POST', '/api/brand'],
            'delete brand' => ['DELETE', '/api/brand/1'],
            'create supplier' => ['POST', '/api/supplier'],
            'update supplier' => ['PUT', '/api/supplier/1'],
            'create email' => ['POST', '/api/email'],
            'update email' => ['PUT', '/api/email/1'],
            'list users' => ['GET', '/api/user'],
            'read user' => ['GET', '/api/user/1'],
            'list transactions' => ['GET', '/api/transaction'],
            'list calendar' => ['GET', '/api/calendar'],
        ];
    }

    #[Test]
    #[DataProvider('endpointsWithoutPermission')]
    public function seller_is_forbidden_on_endpoints_its_role_does_not_grant(string $method, string $uri): void
    {
        $seller = User::factory()->create();
        $seller->assignRole(Role::findOrCreate('Seller', 'web'));

        $response = $this->actingAs($seller, 'api')->json($method, $uri);

        $response->assertForbidden();
    }

    #[Test]
    public function seller_cannot_delete_a_product_of_the_same_company(): void
    {
        $seller = User::factory()->create();
        $seller->assignRole(Role::findOrCreate('Seller', 'web'));
        $product = Product::factory()->create(['company_id' => $seller->company_id]);

        $response = $this->actingAs($seller, 'api')->deleteJson("/api/product/{$product->id}");

        $response->assertForbidden();
        $this->assertNotSoftDeleted('product', ['id' => $product->id]);
    }

    #[Test]
    public function web_guard_permissions_are_honoured_on_api_requests(): void
    {
        $role = Role::findOrCreate('CompanyAdmin', 'web');
        $role->givePermissionTo(Permission::findOrCreate('delete product', 'web'));
        $companyAdmin = User::factory()->create();
        $companyAdmin->assignRole($role);
        $product = Product::factory()->create(['company_id' => $companyAdmin->company_id]);

        $response = $this->actingAs($companyAdmin, 'api')->deleteJson("/api/product/{$product->id}");

        $response->assertOk();
        $this->assertSoftDeleted('product', ['id' => $product->id]);
    }
}
