<?php

declare(strict_types=1);

namespace App\Http\Controllers\Rrhh\Holiday;

use App\Http\Controllers\MainController;
use App\Models\CompanyHoliday;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HolidayDeleteController extends MainController
{
    public function delete(Request $request, int $id): RedirectResponse
    {
        $holiday = CompanyHoliday::where('company_id', Auth::user()->company_id)
            ->where('id', $id)
            ->firstOrFail();
        $holiday->delete();

        return redirect('/rrhh/holidays')->with(['status' => 'success', 'message' => __('Holiday deleted successfully')]);
    }
}
