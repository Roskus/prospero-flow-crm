<?php

namespace Tests\Feature\Controllers\Bank;

use App\Models\Bank;
use Tests\TestCase;

class BankSaveControllerTest extends TestCase
{
    /** @test */
    public function it_can_save_bank(): void
    {
        $data = Bank::factory()->create()->toArray();

        $response = $this->post('bank/save', $data);

        $response->assertRedirect('/bank');
        $this->equalTo(Bank::all()->last(), $data);
    }

    /** @test */
    public function it_can_update_bank(): void
    {
        $data = Bank::factory()->create()->toArray();
        unset($data['uuid']);

        $response = $this->post('bank/save', $data);

        $response->assertRedirect('/bank');
        $this->equalTo(Bank::all()->last(), $data);
    }
}
