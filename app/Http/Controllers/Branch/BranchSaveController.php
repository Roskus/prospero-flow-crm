<?php

declare(strict_types=1);

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\MainController;
use App\Http\Requests\BranchSaveRequest;
use App\Models\Branch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class BranchSaveController extends MainController
{
    public function save(BranchSaveRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $companyId = (int) Auth::user()->company_id;

        $branch = empty($data['id'])
            ? new Branch
            : Branch::ofCompany($companyId)->findOrFail($data['id']);

        $branch->company_id = $companyId;
        $branch->fill(Arr::only($data, ['name', 'code', 'phone', 'email']));
        $branch->address = $branch->address->merge($data);
        $branch->save();

        return redirect('/branch')->with(['status' => 'success', 'message' => __('Branch saved successfully')]);
    }
}
