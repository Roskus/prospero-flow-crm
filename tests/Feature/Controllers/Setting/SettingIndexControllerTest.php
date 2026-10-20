<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Setting;

use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class SettingIndexControllerTest extends TestCase
{
    #[Test]
    public function super_admin_sees_every_settings_group_in_order(): void
    {
        $response = $this->get('/setting');

        $response->assertOk();
        $response->assertSeeTextInOrder([
            'Organization', 'Companies', 'Users', 'Permissions',
            'Finance', 'Bank accounts', 'Banks', 'Currencies', 'Transaction categories',
            'Catalog', 'Brands', 'Categories', 'Products',
            'Communication', 'Email templates',
        ]);
    }

    #[Test]
    public function regular_user_does_not_see_restricted_settings(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::findOrCreate('update company', 'web'));

        $response = $this->actingAs($user)->get('/setting');

        $response->assertOk();
        $response->assertDontSee('href="'.url('/company').'"', false);
        $response->assertDontSee('href="'.url('/user').'"', false);
        $response->assertDontSee('href="'.url('/permission').'"', false);
        $response->assertDontSee('href="'.url('/bank').'"', false);
        $response->assertDontSee('href="'.url('/product').'"', false);
        $response->assertDontSeeText('Organization');
        $response->assertSee('href="'.url('/bank-account').'"', false);
        $response->assertSee('href="'.url('/email-template').'"', false);
    }

    #[Test]
    public function user_without_company_permissions_cannot_access_settings(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/setting');

        $response->assertForbidden();
    }
}
