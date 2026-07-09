<?php

declare(strict_types=1);

namespace App\Http\Controllers\Rrhh\TimeOffApproval;

use App\Http\Controllers\MainController;
use App\Models\TimeOff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimeOffApprovalIndexController extends MainController
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $query = TimeOff::where('company_id', $companyId)
            ->where('status', 'pending');

        $data['requests'] = $query->with('user')->orderBy('created_at', 'asc')->paginate(20);

        return view('rrhh.time_off_approval.index', $data);
    }
}
