<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\MainController;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerImportSaveController extends MainController
{
    public function save(Request $request)
    {
        if (! $request->hasFile('upload')) {
            return redirect('/customer')->withErrors(__("Upload file can't be in blank"));
        }

        $file = $request->file('upload');
        $extension = $file->getClientOriginalExtension();

        //if($extension != 'csv')
        //    return redirect('/customer')->withErrors(__("File upload only accept .csv"));

        $filePath = $file->getPath().DIRECTORY_SEPARATOR.$file->getFilename();

        try {
            $handle = fopen($filePath, 'r');
        } catch (\Throwable $t) {
            return redirect('/customer')->withErrors(__("Can't read uploaded file"));
        }

        // HEADER
        //name;business_name;phone;email;website;country_id;notes;facebook
        $rowCount = 0;
        $separator = ',';
        while (($data = fgetcsv($handle, 1000, $separator)) !== false) {
            //Skip header starting in 1
            if ($data[0] == 'name') {
                continue;
            }

            $country = trim($data[5]);
            $customer = new Customer();
            $customer->company_id = Auth::user()->company_id;
            $customer->name = $data[0];
            $customer->business_name = $data[1];
            $customer->phone = $data[2];
            $customer->email = $data[3];
            $customer->website = rtrim($data[4], '/');
            $customer->country_id = strlen($country) == 2 ? strtolower($country) : '';
            $customer->city = $data[6];
            $customer->notes = $data[7];
            $customer->facebook = (isset($data[8])) ? $data[8] : null;

            $customer->seller_id = Auth::user()->id;
            $customer->created_at = now();
            try {
                $customer->save();
            } catch (\Throwable $t) {
                //Log here
                echo $rowCount;
            }
            $rowCount++;
        }
        fclose($handle);

        $data['row_count'] = $rowCount;

        return redirect('/customer')->with($data);
    }
}
