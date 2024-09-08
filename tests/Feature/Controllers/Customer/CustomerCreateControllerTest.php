<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Customer;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CustomerCreateControllerTest extends TestCase
{
    #[Test]
    public function it_can_create_customer(): void
    {
        $response = $this->get('/customer/create');

        $response->assertOk();
        $response->assertViewIs('customer.customer');
        $response->assertSee(__('Name'));
        $response->assertSee(__('Phone'));
        $response->assertSee(__('Country'));
        $response->assertSee(__('Street'));
        $response->assertSee(__('Facebook'));
        $response->assertSee(__('Status'));
        $response->assertSee(__('Seller'));
    }
}
