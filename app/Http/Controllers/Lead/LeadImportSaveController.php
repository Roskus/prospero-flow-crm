<?php

declare(strict_types=1);

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\MainController;
use App\Http\Requests\ImportRequest;
use App\Models\Lead;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LeadImportSaveController extends MainController
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function save(ImportRequest $request)
    {
        $file = $request->file('upload');
        $filePath = $file->getPath().DIRECTORY_SEPARATOR.$file->getFilename();

        try {
            $handle = fopen($filePath, 'r');
        } catch (\Throwable $t) {
            return redirect('/lead')->withErrors(__("Can't read uploaded file"));
        }

        // HEADER (17)
        //name;business_name;phone;phone2;email;email2;website;country_id;city;notes;facebook;instagram;linkedin;twitter;youtube;tiktok;tags
        $rowCount = 0;
        $separator = (! empty($request->separator)) ? $request->separator : ';';
        while (($data = fgetcsv($handle, 1000, $separator)) !== false) {
            //Skip header starting in 1
            if ($data[0] == 'name') {
                continue;
            }

            // if row is empty continue
            if (empty($data[0])) {
                continue;
            }

            $lead = $this->mapCsvRowToLead($data);

            try {
                $lead->save();
                $rowCount++;
            } catch (\Throwable $t) {
                Log::error($t->getMessage().' | row number:'.($rowCount + 1));
            }
        }
        fclose($handle);

        $status = ($rowCount > 0) ? true : false;

        $response = [
            'status' => $status,
            'message' => ($status) ? 'Leads imported :count successfully' : 'An error occurred while importing leads',
            'count' => $rowCount,
        ];

        return redirect('/lead')->with($response);
    }

    private function mapCsvRowToLead(array $data): Lead
    {
        $country = trim($data[8]);
        $lead = new Lead();

        $lead->company_id = Auth::user()->company_id;

        $lead->name = $data[0];
        $lead->business_name = $data[1];
        $lead->phone = str_replace([' ', '(', ')', '.', '-', '/', '|'], '', $data[2]);
        $lead->phone2 = str_replace([' ', '(', ')', '.', '-', '/', '|'], '', $data[3]);
        $lead->mobile = str_replace([' ', '(', ')', '.', '-', '/', '|'], '', $data[4]);
        $lead->email = $data[5];
        $lead->email2 = $data[6];
        $lead->website = rtrim($data[7], '/');
        $lead->country_id = strlen($country) == 2 ? strtolower($country) : '';
        $lead->city = $data[9];
        $lead->notes = $data[10];
        $lead->facebook = (isset($data[11])) ? $data[11] : null;
        $lead->instagram = (isset($data[12])) ? $data[12] : null;
        $lead->linkedin = (isset($data[13])) ? $data[13] : null;
        $lead->twitter = (isset($data[14])) ? $data[14] : null;
        $lead->youtube = (isset($data[15])) ? $data[15] : null;
        $lead->tiktok = (isset($data[16])) ? $data[16] : null;

        $lead->tags = (isset($data[17])) ? $data[17] : null;

        $lead->seller_id = Auth::user()->id;
        $lead->created_at = now();
        return $lead;
    }
}
