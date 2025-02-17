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
        $data = Bank::factory()->create()->toArray();
        unset($data['uuid']);

        $response = $this->post('bank/save', $data);

        $response->assertRedirect('/bank');
        $this->equalTo(Bank::all()->last(), $data);
    }
}
