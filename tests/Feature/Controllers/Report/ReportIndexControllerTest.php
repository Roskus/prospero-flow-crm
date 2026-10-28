<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Report;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use SplFileInfo;
use Tests\TestCase;

class ReportIndexControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([RoleSeeder::class, PermissionSeeder::class]);
    }

    #[Test]
    public function a_company_admin_sees_every_group_and_report(): void
    {
        $admin = $this->userWithRole('CompanyAdmin');

        $this->actingAs($admin)->get('/report')
            ->assertOk()
            ->assertSeeInOrder([__('Sales'), __('Sales report'), __('Annual sales report'), __('People'), __('Work hours report'), __('Communication'), __('Email report')])
            ->assertSee(url('/report/sale/annual'))
            ->assertSee(url('/report/hours'))
            ->assertSee('setting-tile', false);
    }

    #[Test]
    public function a_seller_does_not_see_the_hours_report(): void
    {
        $seller = $this->userWithRole('Seller');

        $this->actingAs($seller)->get('/report')
            ->assertOk()
            ->assertSee(__('Sales report'))
            ->assertDontSee(__('Work hours report'))
            ->assertDontSee(__('People'));
    }

    #[Test]
    public function an_employee_who_only_manages_their_timesheet_sees_the_hours_report(): void
    {
        $employee = User::factory()->create(['company_id' => $this->user->company_id]);
        $employee->givePermissionTo(Permission::findByName('manage own timesheet', 'web'));

        $this->actingAs($employee)->get('/report')
            ->assertOk()
            ->assertSee(__('Work hours report'))
            ->assertDontSee(__('Sales report'))
            ->assertDontSee(__('Email report'));
    }

    #[Test]
    public function a_group_without_visible_reports_is_hidden(): void
    {
        $user = User::factory()->create(['company_id' => $this->user->company_id]);
        $user->givePermissionTo(Permission::findByName('read report', 'web'));

        $this->actingAs($user)->get('/report')
            ->assertOk()
            ->assertSee(__('Communication'))
            ->assertDontSee(__('Sales report'))
            ->assertDontSee(url('/report/sale'));
    }

    #[Test]
    public function a_user_without_report_permissions_is_forbidden(): void
    {
        $user = User::factory()->create(['company_id' => $this->user->company_id]);

        $this->actingAs($user)->get('/report')->assertForbidden();
    }

    #[Test]
    public function every_report_translation_key_exists_in_english_and_spanish(): void
    {
        $files = [
            ...File::allFiles(resource_path('views/report')),
            ...File::allFiles(app_path('Http/Controllers/Report')),
            app_path('Http/Requests/ReportHoursRequest.php'),
            app_path('Http/Requests/ReportAnnualSaleRequest.php'),
        ];

        $keys = collect($files)
            ->flatMap(function (SplFileInfo|string $file): array {
                preg_match_all('/__\(\s*([\'"])(.*?)(?<!\\\\)\1\s*[,)]/', File::get((string) $file), $matches);

                return $matches[2];
            })
            ->unique();

        foreach (['en', 'es'] as $locale) {
            $translations = json_decode(File::get(lang_path($locale.'.json')), true);

            $this->assertSame([], $keys->reject(fn (string $key): bool => array_key_exists($key, $translations))->values()->all(), "Missing {$locale} translations");
        }
    }

    #[Test]
    public function the_reports_render_in_spanish(): void
    {
        $this->user->update(['lang' => 'es']);

        $this->get('/report')->assertOk()->assertSee('Personas')->assertSee('Reporte anual de ventas')->assertSee('Reporte de horas');
        $this->get('/report/hours')->assertOk()->assertSee('Horas trabajadas')->assertSee('Horas esperadas');
        $this->get('/report/sale/annual')->assertOk()->assertSee('Productos principales')->assertSee('Ventas por vendedor');
    }

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create(['company_id' => $this->user->company_id]);
        $user->assignRole($role);

        return $user;
    }
}
