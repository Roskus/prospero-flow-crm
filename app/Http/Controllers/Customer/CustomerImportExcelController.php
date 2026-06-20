<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\MainController;

class CustomerImportExcelController extends MainController
{
    public function import()
    {
        return view('customer.import-excel');
    }
}
