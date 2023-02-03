<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\MainController;
use App\Imports\CustomerImport;
use Maatwebsite\Excel\Facades\Excel;

class CustomerImportExcelSaveController extends MainController
{
    public function importExcelSave()
    {
        Excel::import(new CustomerImport, request()->file('upload'));

        return redirect('/customer')->with('success', 'All good!');
    }
}
