<?php

declare(strict_types=1);

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\MainController;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PayrollShowController extends MainController
{
    public function show(Request $request, int $id)
    {
        $payroll = Payroll::with('user')->findOrFail($id);

        if (! Auth::user()->hasRole('SuperAdmin') && $payroll->user->company_id !== Auth::user()->company_id) {
            return redirect('/payroll')->with('error', __('Unauthorized'));
        }

        if ($request->has('download')) {
            return Storage::disk('public')->download($payroll->file);
        }

        $data['payroll'] = $payroll;

        return view('payroll.show', $data);
    }
}
