<?php

namespace Tests\Feature\Controllers\Lead;

use Tests\TestCase;

class LeadImportIndexControllerTest extends TestCase
{
    /** @test */
    public function it_can_index_import_lead()
    {
        $response = $this->get('/lead/import');

        $response->assertOk();
        $response->assertViewIs('lead.import');
        $response->assertSee('Download example file');
        $response->assertSee('Save');
        $response->assertSee('Separator');
    }
}
