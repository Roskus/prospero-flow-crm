<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Bank;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BankCreateControllerTest extends TestCase
{
    #[Test]
    public function it_can_create_bank()
    {
        $response = $this->get('/bank/create');

        $response->assertOk();
        $response->assertViewIs('bank.bank');
        $response->assertSee(__('Name'));
        $response->assertSee(__('Phone'));
        $response->assertSee(__('Email'));
        $response->assertSee(__('Website'));
        $response->assertSee(__('Country'));
    }
}
