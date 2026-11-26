<?php

declare(strict_types=1);

namespace App\Http\Controllers\Asset;

use App\Http\Controllers\MainController;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class AssetShowController extends MainController
{
    public function show(int $id): View
    {
        $companyId = (int) Auth::user()->company_id;
        $asset = Asset::query()->ofCompany($companyId)
            ->with([
                'vehicle',
                'machinery',
                'device.carrier',
                'branch',
                'supplier',
                'currentAssignment.user',
                'assignments' => fn ($query) => $query->orderByDesc('start_date')->orderByDesc('id'),
                'assignments.user',
                'assignments.assignedBy',
                'maintenances' => fn ($query) => $query->orderByDesc('date')->orderByDesc('id'),
                'maintenances.supplier',
            ])
            ->findOrFail($id);

        return view('asset.show', [
            'asset' => $asset,
            'maintenanceTotals' => $asset->maintenances->whereNotNull('cost')->groupBy('currency')->map(fn ($records) => $records->sum('cost')),
            'users' => User::where('company_id', $companyId)->where('active', true)->orderBy('first_name')->orderBy('last_name')->get(['id', 'first_name', 'last_name']),
        ]);
    }
}
