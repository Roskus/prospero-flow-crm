<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Bank;

use App\Models\Bank;
use PHPUnit\Framework\Attributes\Test;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class BankSaveControllerTest extends TestCase
{
    #[Test]
    public function it_can_save_bank(): void
    {
        $data = Bank::factory()->create()->toArray();

        $response = $this->post('bank/save', $data);

        $response->assertRedirect('/bank');
        $this->equalTo(Bank::all()->last(), $data);
    }

    #[Test]
    public function it_can_update_bank(): void
    {
        $bank = Bank::factory()->create();
        $data = $bank->toArray();
        $data['name'] = 'Updated Bank Name';

        $response = $this->post('bank/save', $data);

        $response->assertRedirect('/bank');
        $this->assertEquals('Updated Bank Name', Bank::where('bic', $bank->bic)->first()->name);
    }

    #[Test]
    public function it_creates_a_bank_with_a_bic_derived_uuid(): void
    {
        $response = $this->post('bank/save', [
            'name' => 'CaixaBank',
            'country_id' => 'es',
            'bic' => 'CAIXESBBXXX',
        ]);

        $response->assertRedirect('/bank');
        $this->assertDatabaseHas('bank', [
            'uuid' => Uuid::uuid5(Uuid::NAMESPACE_URL, 'CAIXESBBXXX')->toString(),
            'bic' => 'CAIXESBBXXX',
        ]);
    }

    #[Test]
    public function it_rejects_creating_a_bank_with_an_existing_bic(): void
    {
        $bank = Bank::factory()->create(['bic' => 'CAIXESBBXXX']);

        $response = $this->from('/bank/create')->post('bank/save', [
            'name' => 'Duplicate',
            'country_id' => 'es',
            'bic' => $bank->bic,
        ]);

        $response->assertSessionHasErrors('bic');
        $this->assertSame(1, Bank::where('bic', 'CAIXESBBXXX')->count());
    }

    #[Test]
    public function the_bic_cannot_be_changed_when_editing_a_bank(): void
    {
        $bank = Bank::factory()->create(['bic' => 'CAIXESBBXXX']);
        Bank::factory()->create(['bic' => 'BBVAESMMXXX']);

        $response = $this->post('bank/save', [
            'uuid' => $bank->uuid,
            'name' => 'CaixaBank',
            'country_id' => 'es',
            'bic' => 'BBVAESMMXXX',
        ]);

        $response->assertRedirect('/bank');
        $this->assertSame('CAIXESBBXXX', $bank->fresh()->bic);
        $this->assertSame('CaixaBank', $bank->fresh()->name);
    }

    #[Test]
    public function the_edit_form_shows_the_bic_read_only(): void
    {
        $bank = Bank::factory()->create(['bic' => 'CAIXESBBXXX']);

        $response = $this->get('/bank/update/'.$bank->uuid);

        $response->assertOk();
        $response->assertSee('value="CAIXESBBXXX" class="form-control" readonly="readonly"', false);
        $response->assertDontSee('name="bic"', false);
    }
}
