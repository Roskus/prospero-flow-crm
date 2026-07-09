<?php

declare(strict_types=1);

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\MainController;
use App\Http\Requests\PayrollDeleteRequest;
use App\Models\Payroll;
use Illuminate\Support\Facades\Auth;

class PayrollDeleteController extends MainController
{
    public function delete(PayrollDeleteRequest $request, int $id)
    {
        $payroll = Payroll::where('id', $id)->first();

        if (! $payroll) {
            return redirect('payroll')->with('error', __('Payroll not found'));
        }

        $user = Auth::user();
        if (! $user->hasRole('SuperAdmin') && $payroll->user->company_id !== $user->company_id) {
            return redirect('payroll')->with('error', __('Unauthorized'));
        }

        $payroll->delete();

        return redirect('payroll')->with('success', __('Payroll deleted successfully'));
    }
}
