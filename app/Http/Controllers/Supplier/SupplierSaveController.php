<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\MainController;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class SupplierSaveController extends MainController
{
    public function save(Request $request)
    {
        $supplier = (empty($request->id)) ? new Supplier() : Supplier::find($request->id);
        $supplier->company_id = Auth::user()->company_id;
        $supplier->name = $request->name;
        $supplier->phone = $request->phone;
        $supplier->email = $request->email;
        $supplier->website = $request->website;
        $supplier->updated_at = now();
        $supplier->save();
        return redirect('/supplier');
    }
}
