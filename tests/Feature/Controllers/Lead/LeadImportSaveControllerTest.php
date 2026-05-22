<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Lead;

use App\Models\Lead;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LeadImportSaveControllerTest extends TestCase
{
    #[Test]
    public function it_can_import_leads_from_csv()
    {
        $response = $this->post('lead/import/save', []);
        $response->assertRedirect();
        $response->assertSessionHasErrors();

        $baseName = 'hammer_lead_example_20221212';
        $sourcePath = base_path("tests/Feature/Controllers/Lead/{$baseName}.csv");

        // The controller reads from getPath()/{name_without_extension}, copy there
        $tempDir = sys_get_temp_dir();
        copy($sourcePath, "{$tempDir}/{$baseName}.csv");
        copy($sourcePath, "{$tempDir}/{$baseName}");

        $file = new UploadedFile("{$tempDir}/{$baseName}.csv", "{$baseName}.csv", 'text/csv', null, true);
        $response = $this->post('lead/import/save', ['upload' => $file]);
        $response->assertRedirect('/lead');

        $lead = Lead::first();
        $this->assertEquals('John Doe Corp.', $lead->name);
        $this->assertEquals('John Doe Corp.', $lead->business_name);
        $this->assertEquals('1111111111', $lead->phone);
    }
}
