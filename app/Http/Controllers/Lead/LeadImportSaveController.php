<?php

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\MainController;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadImportSaveController extends MainController
{
    public function save(Request $request)
    {
        if(!$request->hasFile('upload'))
            return redirect('/lead')->withErrors(__("Upload file can't be in blank"));

        $file  = $request->file('upload');
        $extension = $file->getClientOriginalExtension();

        //if($extension != 'csv')
        //    return redirect('/lead')->withErrors(__("File upload only accept .csv"));

        $filePath = $file->getPath().DIRECTORY_SEPARATOR.$file->getFilename();

        try {
            $handle = fopen($filePath, 'r');
            $rowCount = 0;
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $rowCount++;
                //Skip header starting in 1
                //name;business_name;phone;email;website;country_id;notes;facebook
                $country = trim($data[5]);
                $lead = new Lead();
                $lead->company_id = Auth::user()->company_id;
                $lead->name = $data[0];
                $lead->business_name = $data[1];
                $lead->phone = $data[2];
                $lead->email = $data[3];
                $lead->website = $data[4];
                $lead->country_id = strlen($country) == 2 ? strtoupper($country) : '';
                $lead->city = $data[6];
                $lead->notes = $data[7];
                $lead->facebook = $data[8];

                $lead->seller_id = Auth::user()->id;
                $lead->created_at = now();
                try {
                    $lead->save();
                } catch (\Throwable $t) {
                    //Log here
                    echo $rowCount;
                }
            }
            fclose($handle);
        } catch (\Throwable $t) {
            //return redirect('/lead')->withErrors(__("Can't read uploaded file"));
        }

        $data['row_count'] = $rowCount;
        return redirect('/lead')->with($data);
    }
}
