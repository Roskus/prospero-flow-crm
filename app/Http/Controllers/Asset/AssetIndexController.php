<?php

declare(strict_types=1);

namespace App\Http\Controllers\Asset;

use App\Enums\AssetCategory;
use App\Enums\AssetStatus;
use App\Http\Controllers\MainController;
use App\Models\Asset;
use App\Models\Branch;
use App\Models\User;
use App\Services\AssetCodeService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssetIndexController extends MainController
{
    /**
     * A search that is exactly an asset code (typed, read by a barcode scanner or from a label QR)
     * opens that asset directly, whatever its category.
     */
    public function index(Request $request, string $category, AssetCodeService $assetCodeService): View|RedirectResponse
    {
        $companyId = (int) Auth::user()->company_id;
        $assetCategory = AssetCategory::from($category);

        if ($request->filled('search')) {
            $scanned = Asset::ofCompany($companyId)->where('code', $assetCodeService->codeFromScan((string) $request->query('search')))->first(['id']);

            if ($scanned !== null) {
                return redirect()->route('asset.show', $scanned->id);
            }
        }
        $status = AssetStatus::tryFrom((string) $request->query('status'));

        $query = Asset::ofCompany($companyId)
            ->ofCategory($assetCategory)
            ->with(['branch', 'currentAssignment.user', $assetCategory === AssetCategory::Vehicle ? 'vehicle' : 'device.carrier']);

        if ($status !== null) {
            $query->where('status', $status->value);
        } else {
            $query->where('status', '!=', AssetStatus::Retired->value);
        }

        if ($request->filled('type')) {
            $query->where('type', (string) $request->query('type'));
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->integer('branch_id'));
        }

        if ($request->filled('user_id')) {
            $query->assignedTo($request->integer('user_id'));
        }

        if ($request->boolean('expiring')) {
            $query->expiringSoon();
        }

        if ($request->filled('search')) {
            $this->applySearch($query, $assetCategory, trim((string) $request->query('search')));
        }

        return view('asset.index', [
            'category' => $assetCategory,
            'assets' => $query->orderBy('code')->paginate(25)->withQueryString(),
            'branches' => Branch::ofCompany($companyId)->orderBy('name')->get(['id', 'name']),
            'users' => User::where('company_id', $companyId)->orderBy('first_name')->orderBy('last_name')->get(['id', 'first_name', 'last_name']),
        ]);
    }

    private function applySearch(Builder $query, AssetCategory $category, string $search): void
    {
        $like = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search).'%';

        $query->where(function (Builder $query) use ($category, $like): void {
            $query->where('code', 'like', $like)
                ->orWhere('name', 'like', $like)
                ->orWhere('brand', 'like', $like)
                ->orWhere('model', 'like', $like)
                ->orWhere('serial_number', 'like', $like);

            if ($category === AssetCategory::Vehicle) {
                $query->orWhereHas('vehicle', fn (Builder $query) => $query->where('license_plate', 'like', $like)->orWhere('vin', 'like', $like));

                return;
            }

            $query->orWhereHas('device', fn (Builder $query) => $query->where('hostname', 'like', $like)
                ->orWhere('imei', 'like', $like)
                ->orWhere('phone_number', 'like', $like)
                ->orWhereHas('carrier', fn (Builder $query) => $query->where('name', 'like', $like)));
        });
    }
}
