<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\MainController;
use App\Http\Requests\ImportRequest;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CustomerImportSaveController extends MainController
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function save(ImportRequest $request)
    {
        $errors = [];
        $file = $request->file('upload');

        // Utiliza una ruta base segura para el almacenamiento de archivos subidos
        $basePath = storage_path('uploads');

        // Genera un nombre seguro para el archivo, sin aceptar la ruta del cliente
        $safeFilename = basename($file->getClientOriginalName());
        $filePath = $basePath.DIRECTORY_SEPARATOR.$safeFilename;

        try {
            $handle = fopen($filePath, 'r');
        } catch (\Throwable $t) {
            return redirect('/customer/import')->withErrors(__("Can't read uploaded file"));
        }

        // HEADER (25)
        //external_id;name;business_name;vat;dob;phone;phone2;mobile;email;email2;website;country_id;province;city;locality;street;zipcode;notes;facebook;instagram;linkedin;twitter;youtube;tiktok;tags
        $rowCount = 0;
        $successfulImports = 0;
        $separator = $request->input('separator', ';');
        while (($data = fgetcsv($handle, 1000, $separator)) !== false) {
            //Skip header starting in 1
            if ($data[0] == 'external_id') {
                continue;
            }

            // if row is empty continue
            if (empty($data[0])) {
                continue;
            }

            $customer = $this->mapCsvRowToCustomer($data);

            try {
                $customer->save();
                $successfulImports++;
            } catch (\Throwable $t) {
                Log::error($t->getMessage().' | row number:'.($rowCount + 1));
                $errors[] = __('Error in row :row. Message: :message', ['row' => $rowCount + 1, 'message' => $t->getMessage()]);
            }
            $rowCount++;
        }
        fclose($handle);

        $status = $successfulImports > 0 ? 'success' : 'error';
        $message = $status === 'success' ? __('Customers imported successfully, with :count errors.', ['count' => count($errors)]) : __('An error occurred while importing customers.');

        $response = [
            'status' => $status,
            'message' => $message,
            'count' => $rowCount,
        ];

        return redirect('/customer')->with($response);
    }

    private function mapCsvRowToCustomer(array $data): Customer
    {
        $country = trim($data[11]);
        $customer = new Customer;

        $customer->company_id = Auth::user()->company_id;
        $customer->external_id = isset($data[0]) ? (int) $data[0] : null;
        $customer->name = $data[1];
        $customer->business_name = $data[2];
        $customer->vat = $data[3];
        try {
            $dob = isset($data[4]) ? Carbon::createFromFormat('d/m/Y', $data[4])->format('Y-m-d') : null;
        } catch (\Throwable $t) {
            $dob = null;
        }
        $customer->dob = $dob;
        $customer->phone = str_replace([' ', '(', ')', '.', '-', '/', '|'], '', $data[5]);
        $customer->phone2 = str_replace([' ', '(', ')', '.', '-', '/', '|'], '', $data[6]);
        $customer->mobile = str_replace([' ', '(', ')', '.', '-', '/', '|'], '', $data[7]);
        $customer->email = $data[8];
        $customer->email2 = $data[9];
        $customer->website = rtrim($data[10], '/');
        $customer->country_id = strlen($country) == 2 ? strtolower($country) : '';
        $customer->province = $data[12];
        $customer->city = $data[13];
        $customer->locality = $data[14];
        $customer->street = $data[15];
        $customer->zipcode = $data[16];
        $customer->notes = $data[17];
        $customer->facebook = (isset($data[18])) ? $data[18] : null;
        $customer->instagram = (isset($data[19])) ? $data[19] : null;
        $customer->linkedin = (isset($data[20])) ? $data[20] : null;
        $customer->twitter = (isset($data[21])) ? $data[21] : null;
        $customer->youtube = (isset($data[22])) ? $data[22] : null;
        $customer->tiktok = (isset($data[23])) ? $data[23] : null;
        if (isset($data[24]) && json_validate($data[24])) {
            $customer->tags = $data[24];
        }

        $customer->seller_id = Auth::user()->id;
        $customer->created_at = now();

        return $customer;
    }
}
