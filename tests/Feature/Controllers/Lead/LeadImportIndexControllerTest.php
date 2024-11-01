<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Lead;

use Tests\TestCase;

class LeadImportIndexControllerTest extends TestCase
{
    #[Test]
    public function it_can_index_import_lead(): void
    {
        $response = $this->get('/lead/import');

        $response->assertOk();
        $response->assertViewIs('lead.import');
        $response->assertSee('Download example file');
        $response->assertSee(__('Save'));
        $response->assertSee(__('Separator'));
    }
}
