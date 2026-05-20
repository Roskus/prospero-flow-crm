<?php

declare(strict_types=1);

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\MainController;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierDeleteController extends MainController
{
    public function delete(Request $request, int $id): RedirectResponse
    {
        $supplier = Supplier::where('id', $id)
            ->where('company_id', Auth::user()->company_id)
            ->firstOrFail();

        $supplier->delete();

        return redirect('/supplier');
    }
}
