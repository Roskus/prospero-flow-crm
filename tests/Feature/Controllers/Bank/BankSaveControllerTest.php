<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Bank;

use App\Models\Bank;
use PHPUnit\Framework\Attributes\Test;
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
}
