<?php

declare(strict_types=1);

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\MainController;
use App\Models\Lead;
use App\Services\CsvExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeadExportController extends MainController
{
    public function __construct(private CsvExportService $csvExportService) {}

    public function export(Request $request): StreamedResponse
    {
        $lead = new Lead;
        $fileName = 'leads_'.Auth::user()->company->name.'_'.date('Ymd_His').'.csv';
        $leads = Lead::where('company_id', Auth::user()->company_id)->get();

        $columns = array_merge(['id'], $lead->getFillable(), ['created_at', 'updated_at']);

        $rows = array_map(function (array $row): array {
            $row['tags'] = is_array($row['tags']) ? implode(separator: ',', array: $row['tags']) : '';
            $row['notes'] = is_null($row['notes']) ? null : str_replace(["\r", "\n"], '-', $row['notes']);
            $row['country_id'] = $row['country']['id'] ?? null;
            $row['seller_id'] = $row['seller']['id'] ?? null;
            $row['industry_id'] = $row['industry']['id'] ?? null;
            $row['company_id'] = $row['company']['id'] ?? null;

            return $row;
        }, $leads->toArray());

        return $this->csvExportService->download($fileName, $columns, $rows);
    }
}
