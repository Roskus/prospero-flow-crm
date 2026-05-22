<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Customer;

use App\Models\Customer;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CustomerExportControllerTest extends TestCase
{
    #[Test]
    public function it_can_export_customer_to_csv()
    {
        Customer::factory()
            ->count(2)
            ->create(['company_id' => $this->user->company_id]);

        $response = $this->get('/customer/export');

        $response->assertOk();
        $this->assertStringStartsWith('text/csv', $response->headers->get('Content-type'));
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('.csv', $response->headers->get('Content-Disposition'));
    }
}
