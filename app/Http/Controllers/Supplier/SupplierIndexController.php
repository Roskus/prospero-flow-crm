<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\MainController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Supplier;

class SupplierIndexController extends MainController
{
    public function index(Request $request)
    {
        $supplier = new Supplier();
        $data['suppliers'] = $supplier->getAllByCompany(Auth::user()->company_id);
        return view('supplier.index', $data);
    }
}
