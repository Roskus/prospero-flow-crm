<?php

declare(strict_types=1);

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\MainController;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LeadExportController extends MainController
{
    public function export(Request $request)
    {
        $separator = ';';
        $lead = new Lead();
        $fileName = 'leads_'.Auth::user()->company->name.'_'.date('Ymd_His').'.csv';
        $leads = Lead::where('company_id', Auth::user()->company_id)->get();
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = array_merge(['id'], $lead->getFillable(), ['created_at', 'updated_at']);
        $rowHeaders = implode($separator, $columns);

        $data = $leads->toArray();

        $content = $rowHeaders."\n";
        foreach ($data as $key => $row) {
            $row['tags'] = is_array($row['tags']) ? implode(separator: ',', array: $row['tags']) : '';
            $row['notes'] = str_replace(["\r", "\n"], '-', $row['notes']);
            $row = str_replace(["\r", "\n"], '', $row);
            $line = implode($separator, $row);
            $content = $content.$line."\n";
        }

        Storage::disk('local')->put($fileName, $content);

        return Storage::download($fileName, null, $headers);
    }
}
