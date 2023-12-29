<?php

declare(strict_types=1);

namespace App\Http\Controllers\Supplier\Contact;

use App\Http\Controllers\MainController;
use App\Models\Supplier\SupplierContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContactExportVCard extends MainController
{
    public function export(Request $request, int $id)
    {
        $contact = SupplierContact::findOrFail($id);
        $fileName = Str::slug(title: $contact->first_name.' '.$contact->last_name, separator: '-').'_'.date('Ymd_His').'.vcf';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];
        $content = $contact->vCard();
        Storage::disk('local')->put($fileName, $content);

        return Storage::download($fileName, null, $headers);
    }
}
