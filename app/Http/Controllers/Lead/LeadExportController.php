<?php

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\MainController;
use Illuminate\Http\Request;
use App\Models\Lead;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LeadExportController extends MainController
{
    public function export(Request $request)
    {
        $content = '';
        $fileName = 'leads_'.Auth::user()->company->name.'_'.date('Ymd_His').'.csv';
        $leads = Lead::where('company_id', Auth::user()->company_id)->get();
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );
        $data = $leads->toArray();

        foreach ($data as $row)
        {
            $content = $row.var_export($row, true);
        }

        Storage::disk('local')->put(storage_path($fileName), $content);
        return Storage::download(storage_path($fileName));
    }

}
