<?php

namespace Tests\Feature\Controllers\Bank;

use App\Models\Bank;
use Tests\TestCase;

class BankUpdateControllerTest extends TestCase
{
    /** @test */
    public function it_can_update_bank(): void
    {
        $bank = Bank::factory()->create();

        $response = $this->get('bank/update/'.$bank->uuid);
        $response->assertSee($bank->name);
        $response->assertSee($bank->country->flag);
        $response->assertSee($bank->phone);
        $response->assertSee($bank->email);
        $response->assertSee($bank->website);
    }
}
