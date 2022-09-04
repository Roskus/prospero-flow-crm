<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\MainController;
use Illuminate\Http\Request;
use App\Models\Supplier;

class SupplierCreateController extends MainController
{
    public function create(Request $request)
    {
        $supplier = new Supplier();
        $data['supplier'] = $supplier;
        return view('supplier.supplier', $data);
    }
}
