<?php

declare(strict_types=1);

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\MainController;
use App\Repositories\SupplierRepository;
use Illuminate\Http\Request;

class SupplierSaveController extends MainController
{
    private SupplierRepository $supplierRepository;

    public function __construct(SupplierRepository $supplierRepository)
    {
        $this->supplierRepository = $supplierRepository;
    }

    public function save(Request $request)
    {
        $supplier = $this->supplierRepository->save($request->all());

        return redirect('/supplier');
    }
}
