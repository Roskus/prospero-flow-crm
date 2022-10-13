<?php

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
        $content = '';
        $fileName = 'leads_'.Auth::user()->company->name.'_'.date('Ymd_His').'.csv';
        $leads = Lead::where('company_id', Auth::user()->company_id)->get();
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];
        /*
        $columns = Scheme::getColumnListing('lead');
        $rowHeaders = implode(',', $columns);
        */
        $data = $leads->toArray();

        //$content = $rowHeaders."\n";
        foreach ($data as $row) {
            $line = implode(',', $row);
            $content = $content.$line."\n";
        }

        Storage::disk('local')->put($fileName, $content);

        return Storage::download($fileName, null, $headers);
    }
}
