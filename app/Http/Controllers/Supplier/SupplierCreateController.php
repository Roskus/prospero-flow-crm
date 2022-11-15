<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\MainController;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierCreateController extends MainController
{
    public function create(Request $request)
    {
        $supplier = new Supplier();
        $data['supplier'] = $supplier;

        return view('supplier.supplier', $data);
    }
}
