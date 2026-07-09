<?php

declare(strict_types=1);

namespace App\Http\Controllers\Rrhh\TimeOffApproval;

use App\Http\Controllers\MainController;
use App\Models\TimeOff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimeOffApprovalSaveController extends MainController
{
    public function approve(Request $request, int $id): RedirectResponse
    {
        $timeOff = TimeOff::where('company_id', Auth::user()->company_id)
            ->where('id', $id)
            ->where('status', 'pending')
            ->firstOrFail();

        $employee = $timeOff->user;
        $isManager = $employee->manager_id === Auth::user()->id;
        $canApprove = $isManager || Auth::user()->can('approve timeoff');

        if (! $canApprove) {
            return redirect()->back()->withErrors(__('You are not authorized to approve this request.'));
        }

        if ($isManager) {
            $timeOff->update([
                'manager_approved_by' => Auth::user()->id,
                'manager_approved_at' => now(),
            ]);
        }

        if (Auth::user()->can('approve timeoff')) {
            $timeOff->update([
                'rrhh_approved_by' => Auth::user()->id,
                'rrhh_approved_at' => now(),
                'status' => 'approved',
            ]);
        }

        if ($timeOff->manager_approved_by && $timeOff->rrhh_approved_by) {
            $timeOff->update(['status' => 'approved']);
        }

        return redirect('/rrhh/approvals')->with(['status' => 'success', 'message' => __('Request approved successfully')]);
    }

    public function reject(Request $request, int $id): RedirectResponse
    {
        $timeOff = TimeOff::where('company_id', Auth::user()->company_id)
            ->where('id', $id)
            ->where('status', 'pending')
            ->firstOrFail();

        $validated = $request->validate([
            'rejected_reason' => 'nullable|string|max:500',
        ]);

        $timeOff->update([
            'status' => 'rejected',
            'rejected_reason' => $validated['rejected_reason'] ?? null,
        ]);

        return redirect('/rrhh/approvals')->with(['status' => 'success', 'message' => __('Request rejected successfully')]);
    }
}
