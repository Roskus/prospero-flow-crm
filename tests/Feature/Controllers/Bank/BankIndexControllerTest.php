<?php

namespace Tests\Feature\Controllers\Bank;

use App\Models\Bank;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BankIndexControllerTest extends TestCase
{
    #[Test]
    public function it_can_index_bank()
    {
        $banks = Bank::factory()->count(2)->create();

        $response = $this->get('/bank');
        $response->assertOk();

        foreach ($banks as $bank) {
            $response->assertSee($bank->name);
            $response->assertSee($bank->country->flag);
            $response->assertSee($bank->bic);
            $response->assertSee($bank->phone);
            $response->assertSee($bank->email);
            $response->assertSee($bank->website);
        }

    }
}
