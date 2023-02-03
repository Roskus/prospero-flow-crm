<?php

declare(strict_types=1);

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
        $supplier->business_name = $request->business_name;
        $supplier->phone = $request->phone;
        $supplier->vat = $request->vat;
        $supplier->email = $request->email;
        $supplier->website = $request->website;
        $supplier->country_id = $request->country_id;
        $supplier->updated_at = now();
        $supplier->save();

        return redirect('/supplier');
    }
}
