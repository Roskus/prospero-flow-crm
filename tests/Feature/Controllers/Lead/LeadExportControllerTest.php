<?php

namespace Tests\Feature\Controllers\Lead;

use App\Models\Lead;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LeadExportControllerTest extends TestCase
{
    #[Test]
    public function it_can_export_lead_to_csv()
    {
        Lead::factory()
            ->count(2)
            ->create(['company_id' => $this->user->company_id]);

        Storage::fake('local');

        $this->get('/lead/export');

        $fileName = 'leads_'.$this->user->company->name.'_'.date('Ymd_His').'.csv';
        Storage::disk('local')->assertExists($fileName);

        $data = Storage::disk('local')->get($fileName);

        $columns = array_merge(['id'], (new Lead)->getFillable(), ['created_at', 'updated_at']);
        $rowHeaders = implode(';', $columns);
        $leads = Lead::all()->toArray();
        $content = $rowHeaders."\n";
        foreach ($leads as $row) {
            $line = [];
            $row['tags'] = is_array($row['tags']) ? implode(separator: ',', array: $row['tags']) : '';
            $row['notes'] = is_null($row['notes']) ? null : str_replace(["\r", "\n"], '-', $row['notes']);
            $row['country_id'] = $row['country']['id'];
            $row['seller_id'] = $row['seller']['id'];
            $row['industry_id'] = $row['industry']['id'];
            $row['company_id'] = $row['company']['id'];
            foreach ($columns as $column) {
                if (isset($row[$column])) {
                    $line[] = $row[$column];
                }
            }
            $line = implode(';', $line);
            $content = $content.$line."\n";
        }

        $this->assertEquals($data, $content);
    }
}
