<?php

declare(strict_types=1);

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\MainController;
use App\Http\Requests\PayrollRequest;
use App\Models\Payroll;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PayrollSaveController extends MainController
{
    public function save(PayrollRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if (empty($request->id)) {
            $payroll = new Payroll;
        } else {
            $payroll = Payroll::whereHas('user', function ($query) {
                $query->where('company_id', Auth::user()->company_id);
            })->findOrFail($request->id);
        }

        $payroll->user_id = $validated['user_id'];
        $payroll->gross_amount = $validated['gross_amount'];
        $payroll->net_amount = $validated['net_amount'];
        $payroll->period_year = $validated['period_year'];
        $payroll->period_month = $validated['period_month'];
        $payroll->payment_date = $validated['payment_date'] ?? null;
        $payroll->iban = $validated['iban'] ?? null;
        $payroll->notes = $validated['notes'] ?? null;

        if ($request->hasFile('file')) {
            try {
                $path = $request->file('file')->store('payroll', 'public');
                $payroll->file = $path;
            } catch (\Throwable $e) {
                Log::error('Payroll file upload failed: '.$e->getMessage());
            }
        }

        $payroll->save();

        return redirect('/payroll')->with(['status' => 'success', 'message' => __('Payroll saved successfully')]);
    }
}
