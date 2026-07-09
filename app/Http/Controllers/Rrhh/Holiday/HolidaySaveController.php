<?php

declare(strict_types=1);

namespace App\Http\Controllers\Rrhh\Holiday;

use App\Http\Controllers\MainController;
use App\Models\CompanyHoliday;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HolidaySaveController extends MainController
{
    public function save(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'name' => 'required|string|max:100',
        ]);

        $exists = CompanyHoliday::where('company_id', Auth::user()->company_id)
            ->where('date', $validated['date'])
            ->exists();

        if ($exists) {
            return redirect()->back()->withErrors(__('A holiday already exists on this date.'))->withInput();
        }

        CompanyHoliday::create([
            'company_id' => Auth::user()->company_id,
            'date' => $validated['date'],
            'name' => $validated['name'],
        ]);

        return redirect('/rrhh/holidays')->with(['status' => 'success', 'message' => __('Holiday saved successfully')]);
    }
}
