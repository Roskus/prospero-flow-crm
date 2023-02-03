<?php

declare(strict_types=1);

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\MainController;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Squire\Models\Country;

class SupplierCreateController extends MainController
{
    public function create(Request $request)
    {
        $supplier = new Supplier();
        $data['supplier'] = $supplier;
        $data['countries'] = Country::orderBy('name')->get();

        return view('supplier.supplier', $data);
    }
}
