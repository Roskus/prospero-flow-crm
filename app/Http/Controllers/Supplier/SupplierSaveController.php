<?php

declare(strict_types=1);

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\MainController;
use App\Repositories\SupplierRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierSaveController extends MainController
{
    private SupplierRepository $supplierRepository;

    public function __construct(SupplierRepository $supplierRepository)
    {
        $this->supplierRepository = $supplierRepository;
    }

    public function save(Request $request)
    {
        if (empty($request->id)) {
            if (Auth::user()->cannot('create supplier')) {
                return redirect('/supplier')->with('error', __('Unauthorized'));
            }
        } elseif (Auth::user()->cannot('update supplier')) {
            return redirect('/supplier')->with('error', __('Unauthorized'));
        }

        $supplier = $this->supplierRepository->save($request->all());

        return redirect('/supplier');
    }
}
