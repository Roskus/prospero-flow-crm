<?php

namespace Tests\Feature\Controllers\Customer;

use Tests\TestCase;

class CustomerImportIndexControllerTest extends TestCase
{
    /** @test */
    public function it_can_index_import_customer()
    {
        $response = $this->get('/customer/import');

        $response->assertOk();
        $response->assertViewIs('customer.import');
        $response->assertSee('Download example file');
        $response->assertSee('Save');
        $response->assertSee('Separator');
    }
}
