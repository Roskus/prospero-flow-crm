<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\MainController;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierUpdateController extends MainController
{
    public function update(Request $request, int $id)
    {
        $data['supplier'] = Supplier::find($id);

        return view('supplier.supplier', $data);
    }
}
