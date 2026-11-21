<?php

declare(strict_types=1);

namespace App\Http\Controllers\Asset\Assignment;

use App\Http\Controllers\MainController;
use App\Models\AssetAssignment;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AssetAssignmentUpdateController extends MainController
{
    public function update(int $id): View
    {
        $companyId = (int) Auth::user()->company_id;
        $assignment = AssetAssignment::whereHas('asset', fn (Builder $query) => $query->where('company_id', $companyId))
            ->with('asset', 'user')
            ->findOrFail($id);

        return view('asset.assignment', [
            'assignment' => $assignment,
            'asset' => $assignment->asset,
            'users' => User::where('company_id', $companyId)->where('active', true)->orderBy('first_name')->orderBy('last_name')->get(['id', 'first_name', 'last_name']),
        ]);
    }
}
