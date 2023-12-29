<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Customer;

use Tests\TestCase;

class CustomerImportIndexControllerTest extends TestCase
{
    /** @test */
    public function it_can_index_import_customer(): void
    {
        $response = $this->get('/customer/import');

        $response->assertOk();
        $response->assertViewIs('customer.import');
        $response->assertSee(__('Download example file'));
        $response->assertSee(__('Save'));
        $response->assertSee(__('Separator'));
    }
}
