<?php

declare(strict_types=1);

namespace App\Http\Controllers\Asset\Assignment;

use App\Http\Controllers\MainController;
use App\Http\Requests\AssetAssignmentSaveRequest;
use App\Services\AssetService;
use Illuminate\Http\RedirectResponse;

class AssetAssignmentSaveController extends MainController
{
    public function save(AssetAssignmentSaveRequest $request, AssetService $assetService): RedirectResponse
    {
        $assignment = $assetService->updateAssignment($request->assignment(), $request->validated());

        return redirect()->route('asset.show', $assignment->asset_id)
            ->with(['status' => 'success', 'message' => __('Assignment saved successfully')]);
    }
}
