<?php

declare(strict_types=1);

namespace App\Http\Controllers\Carrier;

use App\Http\Controllers\MainController;
use App\Models\Carrier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class CarrierDeleteController extends MainController
{
    /**
     * Soft delete: devices that use the carrier keep showing its name.
     */
    public function delete(int $id): RedirectResponse
    {
        Carrier::query()->ofCompany((int) Auth::user()->company_id)->findOrFail($id)->delete();

        return redirect('/carrier')->with(['status' => 'success', 'message' => __('Carrier deleted successfully')]);
    }
}
