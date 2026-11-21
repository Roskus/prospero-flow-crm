<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Carrier;

use App\Models\Carrier;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CarrierControllerTest extends TestCase
{
    #[Test]
    public function it_lists_only_the_carriers_of_the_active_company(): void
    {
        Carrier::factory()->forCompany($this->user->company_id)->create(['name' => 'Movistar']);
        Carrier::factory()->create(['name' => 'Ajena Telecom']);

        $this->get('/carrier')
            ->assertOk()
            ->assertSeeText('Movistar')
            ->assertDontSeeText('Ajena Telecom');
    }

    #[Test]
    public function it_creates_a_carrier(): void
    {
        $this->get('/carrier/create')->assertOk();

        $this->post('/carrier/save', [
            'name' => '  Vodafone ',
            'country_id' => 'ES',
            'phone' => '1444',
            'website' => 'https://www.vodafone.es',
        ])->assertRedirect('/carrier');

        $this->assertDatabaseHas('carrier', [
            'company_id' => $this->user->company_id,
            'name' => 'Vodafone',
            'country_id' => 'es',
            'phone' => '1444',
            'website' => 'https://www.vodafone.es',
        ]);
        $this->get('/carrier')->assertSeeText('Spain');
    }

    #[Test]
    public function the_country_must_be_a_valid_iso_code_and_defaults_to_the_company_country(): void
    {
        $this->user->company->update(['country_id' => 'mx']);

        $html = $this->get('/carrier/create')->assertOk()->getContent();
        $this->assertMatchesRegularExpression('/<option value="mx"\s+selected="selected"/', $html);
        $this->post('/carrier/save', ['name' => 'Telcel', 'country_id' => 'xx'])->assertSessionHasErrors('country_id');
        $this->post('/carrier/save', ['name' => 'Telcel'])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('carrier', ['name' => 'Telcel', 'country_id' => null]);
    }

    #[Test]
    public function it_updates_a_carrier(): void
    {
        $carrier = Carrier::factory()->forCompany($this->user->company_id)->create(['name' => 'Orange']);

        $this->get('/carrier/update/'.$carrier->id)->assertOk()->assertSee('value="Orange"', false);
        $this->post('/carrier/save', ['id' => $carrier->id, 'name' => 'Orange España'])->assertRedirect('/carrier');

        $this->assertSame('Orange España', $carrier->fresh()->name);
    }

    #[Test]
    public function the_name_is_required_and_unique_in_the_company(): void
    {
        Carrier::factory()->forCompany($this->user->company_id)->create(['name' => 'Movistar']);
        Carrier::factory()->forCompany($this->user->company_id)->create(['name' => 'Yoigo'])->delete();
        Carrier::factory()->create(['name' => 'Digi']);

        $this->post('/carrier/save', ['name' => ''])->assertSessionHasErrors('name');
        $this->post('/carrier/save', ['name' => 'Movistar', 'country_id' => 'es'])->assertSessionHasErrors('name');
        $this->post('/carrier/save', ['name' => 'Yoigo', 'country_id' => 'es'])->assertSessionHasNoErrors();
        $this->post('/carrier/save', ['name' => 'Digi', 'country_id' => 'es'])->assertSessionHasNoErrors();
        $this->post('/carrier/save', ['name' => 'Bad', 'website' => 'not a url'])->assertSessionHasErrors('website');
    }

    #[Test]
    public function the_same_name_is_allowed_in_another_country_but_not_twice_in_one(): void
    {
        $spanish = Carrier::factory()->forCompany($this->user->company_id)->create(['name' => 'Movistar', 'country_id' => 'es']);

        $this->post('/carrier/save', ['name' => 'Movistar', 'country_id' => 'ar'])->assertSessionHasNoErrors();
        $this->post('/carrier/save', ['name' => 'Movistar', 'country_id' => 'AR'])->assertSessionHasErrors('name');
        $this->post('/carrier/save', ['name' => 'Movistar'])->assertSessionHasNoErrors();
        $this->post('/carrier/save', ['name' => 'Movistar'])->assertSessionHasErrors('name');
        $this->post('/carrier/save', ['id' => $spanish->id, 'name' => 'Movistar', 'country_id' => 'ar'])->assertSessionHasErrors('name');
        $this->post('/carrier/save', ['id' => $spanish->id, 'name' => 'Movistar', 'country_id' => 'es', 'phone' => '1004'])->assertSessionHasNoErrors();

        $this->assertSame(3, Carrier::query()->ofCompany($this->user->company_id)->where('name', 'Movistar')->count());
    }

    #[Test]
    public function a_carrier_of_another_company_is_not_found(): void
    {
        $other = Carrier::factory()->create(['name' => 'Ajena']);

        $this->get('/carrier/update/'.$other->id)->assertNotFound();
        $this->post('/carrier/save', ['id' => $other->id, 'name' => 'PWNED'])->assertNotFound();
        $this->delete('/carrier/delete/'.$other->id)->assertNotFound();

        $this->assertSame('Ajena', $other->fresh()->name);
        $this->assertNull($other->fresh()->deleted_at);
    }

    #[Test]
    public function deleting_a_carrier_is_soft(): void
    {
        $carrier = Carrier::factory()->forCompany($this->user->company_id)->create(['name' => 'Orange']);

        $this->delete('/carrier/delete/'.$carrier->id)->assertRedirect('/carrier');

        $this->assertSoftDeleted($carrier);
        $this->get('/carrier')->assertDontSeeText('Orange');
    }

    #[Test]
    public function permissions_are_required(): void
    {
        $carrier = Carrier::factory()->forCompany($this->user->company_id)->create();
        $reader = User::factory()->create(['company_id' => $this->user->company_id]);
        $reader->givePermissionTo(Permission::findOrCreate('read asset', 'web'));
        $stranger = User::factory()->create(['company_id' => $this->user->company_id]);

        $this->actingAs($stranger)->get('/carrier')->assertForbidden();

        $this->actingAs($reader)->get('/carrier')->assertOk()->assertDontSee('/carrier/create');
        $this->actingAs($reader)->get('/carrier/create')->assertForbidden();
        $this->actingAs($reader)->get('/carrier/update/'.$carrier->id)->assertForbidden();
        $this->actingAs($reader)->post('/carrier/save', ['name' => 'Nuevo'])->assertForbidden();
        $this->actingAs($reader)->delete('/carrier/delete/'.$carrier->id)->assertForbidden();
    }

    #[Test]
    public function the_settings_page_shows_the_carriers_tile_only_with_read_asset(): void
    {
        $this->get('/setting')->assertOk()->assertSee(url('/carrier'));

        $companyReader = User::factory()->create(['company_id' => $this->user->company_id]);
        $companyReader->givePermissionTo(Permission::findOrCreate('read company', 'web'));

        $this->actingAs($companyReader)->get('/setting')->assertOk()->assertDontSee(url('/carrier'));
    }

    #[Test]
    public function an_edit_without_country_is_checked_and_saved_without_country(): void
    {
        $orange = Carrier::factory()->forCompany($this->user->company_id)->create(['name' => 'Orange', 'country_id' => 'es']);
        Carrier::factory()->forCompany($this->user->company_id)->create(['name' => 'Movistar', 'country_id' => 'es']);

        $this->post('/carrier/save', ['id' => $orange->id, 'name' => 'Movistar'])->assertSessionHasNoErrors();

        $orange->refresh();
        $this->assertNull($orange->country_id);
        $this->assertSame(1, Carrier::query()->ofCompany($this->user->company_id)->where('name', 'Movistar')->where('country_id', 'es')->count());
    }
}
