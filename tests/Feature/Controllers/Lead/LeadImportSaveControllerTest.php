<?php

namespace Tests\Feature\Controllers\Lead;

use App\Models\Lead;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class LeadImportSaveControllerTest extends TestCase
{
    /** @test */
    public function it_can_import_leads_from_csv()
    {
        $response = $this->post('lead/import/save', []);
        $response->assertRedirect();
        $response->assertSessionHasErrors();

        $path = str_replace('\\', DIRECTORY_SEPARATOR, base_path('tests\Feature\Controllers\Lead\hammer_lead_example_20221212.csv'));
        $file = new UploadedFile($path, 'hammer_lead_example_20221212.csv');
        $response = $this->post('lead/import/save', ['upload' => $file]);
        $response->assertRedirect('/lead');

        $customer = Lead::first();
        $this->assertEquals('John Doe Corp.', $customer->name);
        $this->assertEquals('John Doe Corp.', $customer->business_name);
        $this->assertEquals('1111111111', $customer->phone);
    }
}
