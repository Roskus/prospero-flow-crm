<?php

declare(strict_types=1);

namespace App\Http\Controllers\Asset\Assignment;

use App\Http\Controllers\MainController;
use App\Http\Requests\AssetAssignmentSaveRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;

class AssetAssignmentSaveController extends MainController
{
    public function save(AssetAssignmentSaveRequest $request): RedirectResponse
    {
        $assignment = $request->assignment();
        $assignment->fill(Arr::only($request->validated(), ['user_id', 'start_date', 'end_date', 'notes']));
        $assignment->save();

        return redirect()->route('asset.show', $assignment->asset_id)
            ->with(['status' => 'success', 'message' => __('Assignment saved successfully')]);
    }
}
