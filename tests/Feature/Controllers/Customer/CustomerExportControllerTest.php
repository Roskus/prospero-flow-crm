<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Customer;

use App\Models\Customer;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CustomerExportControllerTest extends TestCase
{
    #[Test]
    public function it_can_export_customer_to_csv()
    {
        Customer::factory()
            ->count(2)
            ->create(['company_id' => $this->user->company_id]);

        $response = $this->get('/customer/export');

        $response->assertOk();
        $this->assertStringStartsWith('text/csv', $response->headers->get('Content-type'));
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('.csv', $response->headers->get('Content-Disposition'));
    }

    #[Test]
    public function it_neutralizes_formula_injection_in_exported_values(): void
    {
        Customer::factory()->create([
            'company_id' => $this->user->company_id,
            'name' => "=cmd|' /C calc'!A0",
            'business_name' => 'Foo;Bar',
        ]);

        $content = $this->get('/customer/export')->streamedContent();

        $this->assertStringContainsString(";\"'=cmd|' /C calc'!A0\";\"Foo;Bar\";", $content);
        $this->assertStringNotContainsString(';=cmd|', $content);
    }

    #[Test]
    public function it_only_exports_customers_of_the_user_company(): void
    {
        $ownCustomer = Customer::factory()->create(['company_id' => $this->user->company_id]);
        $foreignCustomer = Customer::factory()->create();

        $content = $this->get('/customer/export')->streamedContent();

        $this->assertStringContainsString($ownCustomer->name, $content);
        $this->assertStringNotContainsString($foreignCustomer->name, $content);
    }

    #[Test]
    public function user_without_export_permission_cannot_export_customers(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('Seller', 'web'));

        $response = $this->actingAs($user)->get('/customer/export');

        $response->assertForbidden();
    }
}
