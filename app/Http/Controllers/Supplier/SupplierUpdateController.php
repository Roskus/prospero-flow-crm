<?php

declare(strict_types=1);

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\MainController;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Squire\Models\Country;

class SupplierUpdateController extends MainController
{
    public function update(Request $request, int $id)
    {
        $data['countries'] = Country::orderBy('name')->get();
        $data['supplier'] = Supplier::where('company_id', Auth::user()->company_id)->findOrFail($id);

        return view('supplier.supplier', $data);
    }
}
