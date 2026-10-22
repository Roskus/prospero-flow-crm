<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Branch;

use App\Models\Branch;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Symfony\Component\Finder\SplFileInfo;
use Tests\TestCase;

class BranchControllerTest extends TestCase
{
    #[Test]
    public function it_lists_only_the_branches_of_the_active_company(): void
    {
        Branch::factory()->forCompany($this->user->company_id)->create(['name' => 'Sucursal Norte', 'city' => 'Rosario']);
        Branch::factory()->create(['name' => 'Sucursal ajena']);

        $this->get('/branch')
            ->assertOk()
            ->assertSeeTextInOrder([$this->user->company->name, 'Main branch', 'Sucursal Norte'])
            ->assertSeeText('Rosario')
            ->assertDontSeeText('Sucursal ajena');
    }

    #[Test]
    public function a_branch_of_another_company_is_not_found(): void
    {
        $otherBranch = Branch::factory()->create();

        $this->get('/branch/update/'.$otherBranch->id)->assertNotFound();
        $this->post('/branch/save', ['id' => $otherBranch->id, 'name' => 'PWNED', 'code' => 'X1'])->assertNotFound();
        $this->delete('/branch/delete/'.$otherBranch->id)->assertNotFound();
        $this->post('/branch/main/'.$otherBranch->id)->assertNotFound();

        $this->assertNotSame('PWNED', $otherBranch->fresh()->name);
    }

    #[Test]
    public function users_who_can_only_read_the_company_cannot_change_branches(): void
    {
        $reader = User::factory()->create(['company_id' => $this->user->company_id]);
        $reader->givePermissionTo(Permission::findOrCreate('read company', 'web'));
        $branch = Branch::factory()->forCompany($this->user->company_id)->create();

        $this->actingAs($reader)->get('/branch')->assertOk()->assertDontSee('/branch/create');
        $this->actingAs($reader)->get('/branch/create')->assertForbidden();
        $this->actingAs($reader)->post('/branch/save', ['name' => 'Nueva', 'code' => '0009'])->assertForbidden();
        $this->actingAs($reader)->delete('/branch/delete/'.$branch->id)->assertForbidden();
        $this->actingAs($reader)->post('/branch/main/'.$branch->id)->assertForbidden();
    }

    #[Test]
    public function the_new_branch_form_suggests_the_next_code(): void
    {
        Branch::factory()->forCompany($this->user->company_id)->create(['code' => '0002']);

        $this->get('/branch/create')
            ->assertOk()
            ->assertSee('value="0003"', false)
            ->assertSee('id="map"', false);
    }

    #[Test]
    public function it_creates_a_branch_with_its_address(): void
    {
        $this->post('/branch/save', [
            'name' => 'Centro',
            'code' => '0002',
            'phone' => '+34910000000',
            'email' => 'centro@example.com',
            'country_id' => 'ES',
            'city' => 'Madrid',
            'street' => 'Calle Mayor 1',
            'address_extra' => 'Local 2',
        ])->assertRedirect('/branch');

        $this->assertDatabaseHas('branch', [
            'company_id' => $this->user->company_id,
            'name' => 'Centro',
            'code' => '0002',
            'is_main' => false,
            'country_id' => 'es',
            'city' => 'Madrid',
            'street' => 'Calle Mayor 1',
            'address_extra' => 'Local 2',
        ]);
    }

    #[Test]
    public function it_updates_a_branch(): void
    {
        $branch = Branch::factory()->forCompany($this->user->company_id)->create(['code' => '0002', 'city' => 'Madrid']);

        $this->post('/branch/save', ['id' => $branch->id, 'name' => 'Renamed', 'code' => '0002'])->assertRedirect('/branch');

        $branch->refresh();
        $this->assertSame('Renamed', $branch->name);
        $this->assertSame('Madrid', $branch->city);
    }

    #[Test]
    public function the_code_is_unique_in_the_company_including_deleted_branches(): void
    {
        Branch::factory()->forCompany($this->user->company_id)->create(['code' => '0002']);
        Branch::factory()->forCompany($this->user->company_id)->create(['code' => '0003'])->delete();
        Branch::factory()->create(['code' => '0004']);

        $this->post('/branch/save', ['name' => 'Duplicated', 'code' => '0002'])->assertSessionHasErrors('code');
        $this->post('/branch/save', ['name' => 'Deleted code', 'code' => '0003'])->assertSessionHasErrors('code');
        $this->post('/branch/save', ['name' => 'Other company code', 'code' => '0004'])->assertSessionHasNoErrors();
    }

    #[Test]
    public function the_main_branch_cannot_be_deleted(): void
    {
        $main = $this->user->company->mainBranch;

        $this->delete('/branch/delete/'.$main->id)
            ->assertRedirect('/branch')
            ->assertSessionHas('status', 'error');

        $this->assertNotSoftDeleted('branch', ['id' => $main->id]);
    }

    #[Test]
    public function a_deleted_branch_disappears_and_its_users_work_from_the_main_branch(): void
    {
        $branch = Branch::factory()->forCompany($this->user->company_id)->create(['name' => 'Sucursal Norte']);
        $user = User::factory()->create(['company_id' => $this->user->company_id, 'default_branch_id' => $branch->id]);

        $this->delete('/branch/delete/'.$branch->id)->assertRedirect('/branch')->assertSessionHas('status', 'success');

        $this->assertSoftDeleted('branch', ['id' => $branch->id]);
        $this->get('/branch')->assertDontSeeText('Sucursal Norte');
        $this->get('/user/update/'.$user->id)->assertDontSeeText('Sucursal Norte');
        $this->assertTrue($user->fresh()->currentBranch()->is($this->user->company->mainBranch));
    }

    #[Test]
    public function it_sets_another_main_branch(): void
    {
        $previous = $this->user->company->mainBranch;
        $branch = Branch::factory()->forCompany($this->user->company_id)->create();

        $this->post('/branch/main/'.$branch->id)->assertRedirect('/branch')->assertSessionHas('status', 'success');

        $this->assertTrue($branch->fresh()->is_main);
        $this->assertFalse($previous->fresh()->is_main);
    }

    #[Test]
    public function every_branch_translation_key_exists_in_english_and_spanish(): void
    {
        $files = [
            ...File::allFiles(resource_path('views/branch')),
            ...File::allFiles(app_path('Http/Controllers/Branch')),
            new SplFileInfo(app_path('Http/Requests/BranchSaveRequest.php'), '', 'BranchSaveRequest.php'),
        ];

        $keys = collect($files)
            ->flatMap(function (SplFileInfo $file): array {
                preg_match_all('/__\(\s*([\'"])(.*?)(?<!\\\\)\1\s*[,)]/', $file->getContents(), $matches);

                return $matches[2];
            })
            ->push('Branches', 'Default branch', 'Legal address of the company. The address of each location is managed in')
            ->unique();

        foreach (['en', 'es'] as $locale) {
            $translations = json_decode(File::get(lang_path($locale.'.json')), true);

            $this->assertSame([], $keys->reject(fn (string $key): bool => array_key_exists($key, $translations))->values()->all(), "Missing {$locale} translations");
        }
    }

    #[Test]
    public function the_branch_screen_is_rendered_in_spanish(): void
    {
        App::setLocale('es');
        $this->user->update(['lang' => 'es']);

        $this->get('/branch')->assertOk()->assertSeeText('Sucursales')->assertSeeText('Casa matriz');
    }

    #[Test]
    public function a_company_created_by_a_super_admin_does_not_add_branches_to_the_active_company(): void
    {
        Company::factory()->create();

        $this->assertSame(1, Branch::ofCompany($this->user->company_id)->count());
    }
}
