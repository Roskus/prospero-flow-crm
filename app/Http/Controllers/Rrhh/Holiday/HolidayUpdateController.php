<?php

declare(strict_types=1);

namespace App\Http\Controllers\Rrhh\Holiday;

use App\Http\Controllers\MainController;
use App\Models\CompanyHoliday;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HolidayUpdateController extends MainController
{
    public function update(Request $request, int $id): RedirectResponse
    {
        $holiday = CompanyHoliday::where('company_id', Auth::user()->company_id)
            ->where('id', $id)
            ->firstOrFail();

        $validated = $request->validate([
            'date' => 'required|date',
            'name' => 'required|string|max:100',
        ]);

        $exists = CompanyHoliday::where('company_id', Auth::user()->company_id)
            ->where('date', $validated['date'])
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return redirect()->back()->withErrors(__('A holiday already exists on this date.'))->withInput();
        }

        $holiday->update($validated);

        return redirect('/rrhh/holidays')->with(['status' => 'success', 'message' => __('Holiday updated successfully')]);
    }
}
