<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AcquisitionType;
use App\Enums\AssetCategory;
use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetDevice;
use App\Models\AssetMaintenance;
use App\Models\AssetVehicle;
use App\Models\Branch;
use App\Models\Carrier;
use App\Models\Company;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class AssetService
{
    const string PHOTO_DISK = 'local';

    const array ASSET_FIELDS = [
        'type', 'code', 'name', 'brand', 'model', 'serial_number', 'branch_id', 'supplier_id',
        'acquisition_type', 'purchase_date', 'purchase_price', 'rental_monthly_fee', 'rental_starts_at', 'rental_ends_at',
        'currency', 'warranty_until', 'notes',
    ];

    const array PURCHASE_FIELDS = ['purchase_date', 'purchase_price'];

    const array RENTING_FIELDS = ['rental_monthly_fee', 'rental_starts_at', 'rental_ends_at'];

    const array VEHICLE_FIELDS = [
        'license_plate', 'vin', 'manufacturing_year', 'fuel_type', 'odometer_km', 'usage_hours',
        'insurance_company', 'insurance_policy', 'insurance_expires_at', 'inspection_expires_at',
    ];

    const array MACHINERY_FIELDS = [
        'capacity', 'certification_number', 'certified_by',
        'certification_expires_at', 'calibration_expires_at', 'safety_inspection_expires_at',
    ];

    const array DEVICE_FIELDS = [
        'hostname', 'ip_address', 'mac_address', 'operating_system', 'cpu', 'ram', 'storage', 'specs',
        'imei', 'carrier_id', 'phone_number', 'sim_pin', 'sim_puk',
    ];

    /**
     * Data the asset form needs: the asset and the company catalogs to pick from.
     *
     * @return array<string, mixed>
     */
    public function formData(Asset $asset): array
    {
        $company = Auth::user()->company;

        return [
            'asset' => $asset,
            'branches' => Branch::query()->ofCompany($company->id)->orderBy('name')->get(['id', 'name']),
            // Active suppliers plus the recorded one, even if deleted since, so saving keeps it.
            'suppliers' => Supplier::withTrashed()
                ->where('company_id', $company->id)
                ->where(fn (Builder $query) => $query->whereNull('deleted_at')->orWhere('id', $asset->supplier_id))
                ->orderBy('name')
                ->get(['id', 'name', 'deleted_at']),
            'carriers' => Carrier::query()->ofCompany($company->id)->orderBy('name')->get(['id', 'name']),
            'users' => User::where('company_id', $company->id)->where('active', true)->orderBy('first_name')->orderBy('last_name')->get(['id', 'first_name', 'last_name']),
            'currencies' => $company->usableCurrencies(array_filter([$asset->currency])),
        ];
    }

    /**
     * Saves the asset with its category detail and photo. The status picked in the form drives the
     * assignment: "assigned" opens one for the chosen user, any other status closes the open one.
     *
     * @param  array<string, mixed>  $data  validated data
     */
    public function save(Asset $asset, array $data, int $companyId, ?UploadedFile $photo = null): Asset
    {
        $oldPhotoPath = null;
        $newPhotoPath = $photo?->store('asset/'.$companyId, self::PHOTO_DISK);

        try {
            $asset = DB::transaction(function () use ($asset, $data, $companyId, $newPhotoPath, &$oldPhotoPath): Asset {
                $this->lockCompany($companyId);

                // Reload under its row lock: a concurrent assign or return may have changed it
                // after the form request loaded it.
                if ($asset->exists) {
                    $asset = Asset::query()->with('vehicle', 'device', 'machinery')->lockForUpdate()->findOrFail($asset->id);
                }
                $oldPhotoPath = $asset->photo_path;

                $this->ensureUniqueIdentifiers($asset, $data, $companyId);
                $isNew = ! $asset->exists;
                $asset->company_id = $companyId;
                $asset->category = $isNew ? $data['category'] : $asset->category;
                $asset->fill(Arr::only($data, self::ASSET_FIELDS));
                $asset->acquisition_type ??= AcquisitionType::Purchase;
                $asset->fill(array_fill_keys($asset->isRenting() ? self::PURCHASE_FIELDS : self::RENTING_FIELDS, null));

                if (empty($data['currency']) && (isset($data['purchase_price']) || isset($data['rental_monthly_fee']))) {
                    $asset->currency = $asset->company()->value('currency');
                }

                if ($newPhotoPath !== null) {
                    $asset->photo_path = $newPhotoPath;
                } elseif (! empty($data['remove_photo'])) {
                    $asset->photo_path = null;
                }

                $requestedStatus = AssetStatus::tryFrom((string) ($data['status'] ?? ''));
                $asset->status = $requestedStatus ?? ($isNew ? AssetStatus::Available : $asset->status);
                $asset->save();

                if ($requestedStatus !== null) {
                    $holderId = $requestedStatus === AssetStatus::Assigned ? (int) $data['assigned_user_id'] : null;
                    $this->syncAssignment($asset, $holderId);
                }

                $this->saveDetail($asset, $data);

                return $asset;
            });
        } catch (Throwable $throwable) {
            if ($newPhotoPath !== null) {
                Storage::disk(self::PHOTO_DISK)->delete($newPhotoPath);
            }

            throw $throwable;
        }

        if ($oldPhotoPath !== null && $oldPhotoPath !== $asset->photo_path) {
            Storage::disk(self::PHOTO_DISK)->delete($oldPhotoPath);
        }

        return $asset;
    }

    /**
     * Closes the open assignment when the holder changes or goes away, and opens one for the new holder.
     */
    private function syncAssignment(Asset $asset, ?int $holderId): void
    {
        $current = $asset->currentAssignment()->first();

        if ($current !== null && $current->user_id === $holderId) {
            return;
        }

        // The handover happens on one date: today, or the start of an assignment scheduled ahead.
        $handoverDate = $current?->start_date->max(today()) ?? today();

        if ($current !== null) {
            $current->end_date = $handoverDate;
            $current->save();
        }

        if ($holderId !== null) {
            if ($this->assignmentOverlaps($asset->id, $handoverDate, null)) {
                throw ValidationException::withMessages(['assigned_user_id' => __('The dates overlap with another assignment of this asset.')]);
            }

            $asset->assignments()->create([
                'user_id' => $holderId,
                'assigned_by' => Auth::id(),
                'start_date' => $handoverDate,
            ]);
        }

        $asset->unsetRelation('currentAssignment');
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function saveDetail(Asset $asset, array $data): void
    {
        if ($asset->category === AssetCategory::Vehicle) {
            $asset->vehicle()->updateOrCreate([], Arr::only(array_merge(array_fill_keys(self::VEHICLE_FIELDS, null), $data), self::VEHICLE_FIELDS));
            $this->saveMachineryDetail($asset, $data);

            return;
        }

        $detail = Arr::only(array_merge(array_fill_keys(self::DEVICE_FIELDS, null), $data), self::DEVICE_FIELDS);

        if (! $asset->type->hasPhoneLine()) {
            $detail = array_merge($detail, ['imei' => null, 'carrier_id' => null, 'phone_number' => null, 'sim_pin' => null, 'sim_puk' => null]);
        }

        $asset->device()->updateOrCreate([], $detail);
    }

    /**
     * Machinery types keep their certifications and inspections; when the type changes to a
     * non-machinery one (a crane recorded by mistake as a truck), the machinery detail goes away.
     *
     * @param  array<string, mixed>  $data
     */
    private function saveMachineryDetail(Asset $asset, array $data): void
    {
        if (! $asset->type->isMachinery()) {
            $asset->machinery()->delete();
            $asset->unsetRelation('machinery');

            return;
        }

        $asset->machinery()->updateOrCreate([], Arr::only(array_merge(array_fill_keys(self::MACHINERY_FIELDS, null), $data), self::MACHINERY_FIELDS));
    }

    public function assign(Asset $asset, int $userId, ?string $startDate, ?string $notes, int $assignedBy): AssetAssignment
    {
        return DB::transaction(function () use ($asset, $userId, $startDate, $notes, $assignedBy): AssetAssignment {
            $asset = Asset::query()->lockForUpdate()->findOrFail($asset->id);

            if ($asset->status !== AssetStatus::Available) {
                throw ValidationException::withMessages([
                    'user_id' => $asset->status === AssetStatus::Assigned
                        ? __('The asset must be returned before assigning it again.')
                        : __('Only available assets can be assigned.'),
                ]);
            }

            $start = $startDate !== null ? Carbon::parse($startDate)->startOfDay() : today();
            if ($this->assignmentOverlaps($asset->id, $start, null)) {
                throw ValidationException::withMessages(['start_date' => __('The dates overlap with another assignment of this asset.')]);
            }

            $assignment = $asset->assignments()->create([
                'user_id' => $userId,
                'assigned_by' => $assignedBy,
                'start_date' => $start,
                'notes' => $notes,
            ]);

            $asset->update(['status' => AssetStatus::Assigned]);

            return $assignment;
        });
    }

    public function return(Asset $asset, ?string $endDate, ?string $notes): AssetAssignment
    {
        return DB::transaction(function () use ($asset, $endDate, $notes): AssetAssignment {
            $asset = Asset::query()->lockForUpdate()->findOrFail($asset->id);
            $assignment = $asset->currentAssignment()->first();

            if ($assignment === null) {
                throw ValidationException::withMessages(['end_date' => __('The asset is not assigned.')]);
            }

            $end = $endDate !== null ? Carbon::parse($endDate)->startOfDay() : today();
            if ($end->lt($assignment->start_date)) {
                throw ValidationException::withMessages(['end_date' => __('The return date cannot be earlier than the assignment start date.')]);
            }

            $assignment->end_date = $end;
            if (! empty($notes)) {
                $assignment->notes = trim(($assignment->notes ? $assignment->notes."\n" : '').$notes);
            }
            $assignment->save();

            $asset->update(['status' => AssetStatus::Available]);

            return $assignment;
        });
    }

    /**
     * Corrects a recorded assignment. The overlap check is repeated under the asset lock, so two
     * concurrent corrections of the same asset cannot both pass it.
     *
     * @param  array<string, mixed>  $data  validated data
     */
    public function updateAssignment(AssetAssignment $assignment, array $data): AssetAssignment
    {
        return DB::transaction(function () use ($assignment, $data): AssetAssignment {
            Asset::query()->whereKey($assignment->asset_id)->lockForUpdate()->first();
            $assignment = AssetAssignment::query()->findOrFail($assignment->id);

            // The asset may have been returned after the form was validated as an open assignment.
            if (! $assignment->isOpen() && empty($data['end_date'])) {
                throw ValidationException::withMessages(['end_date' => __('The assignment changed meanwhile. Reload the page and try again.')]);
            }

            $start = Carbon::parse((string) $data['start_date'])->startOfDay();
            $end = $assignment->isOpen() ? null : Carbon::parse((string) $data['end_date'])->startOfDay();

            if ($this->assignmentOverlaps($assignment->asset_id, $start, $end, $assignment->id)) {
                throw ValidationException::withMessages(['start_date' => __('The dates overlap with another assignment of this asset.')]);
            }

            $assignment->fill(Arr::only($data, ['user_id', 'start_date', 'end_date', 'notes']));
            $assignment->save();

            return $assignment;
        });
    }

    /**
     * Whether the period overlaps another assignment of the asset. A null end means still open.
     * Periods may touch: one assignment can end the day the next one starts.
     */
    public function assignmentOverlaps(int $assetId, Carbon $start, ?Carbon $end, ?int $ignoreAssignmentId = null): bool
    {
        return AssetAssignment::query()
            ->where('asset_id', $assetId)
            ->when($ignoreAssignmentId !== null, fn (Builder $query) => $query->whereKeyNot($ignoreAssignmentId))
            ->where(fn (Builder $query) => $query->whereNull('end_date')->orWhere('end_date', '>', $start->toDateString()))
            ->when($end !== null, fn (Builder $query) => $query->where('start_date', '<', $end->toDateString()))
            ->exists();
    }

    /**
     * Repeats the code and IMEI uniqueness checks under the company lock: the form request checks
     * them first, but two concurrent saves (a double submit) could both pass that check. Codes of
     * deleted assets stay taken, so an old label never opens another asset.
     *
     * @param  array<string, mixed>  $data
     */
    private function ensureUniqueIdentifiers(Asset $asset, array $data, int $companyId): void
    {
        $codeTaken = Asset::withTrashed()->ofCompany($companyId)
            ->where('code', $data['code'] ?? $asset->code)
            ->when($asset->exists, fn (Builder $query) => $query->whereKeyNot($asset->id))
            ->exists();

        if ($codeTaken) {
            throw ValidationException::withMessages(['code' => __('This code is already used by another asset, or was used by a deleted one.')]);
        }

        if (empty($data['imei'])) {
            return;
        }

        $imeiTaken = AssetDevice::query()
            ->where('imei', $data['imei'])
            ->whereHas('asset', fn (Builder $query) => $query->where('company_id', $companyId)->when($asset->exists, fn (Builder $query) => $query->whereKeyNot($asset->id)))
            ->exists();

        if ($imeiTaken) {
            throw ValidationException::withMessages(['imei' => __('This IMEI is already registered on another asset.')]);
        }
    }

    public function lockCompany(int $companyId): void
    {
        Company::withTrashed()->whereKey($companyId)->lockForUpdate()->first();
    }

    /**
     * @param  array<string, mixed>  $data  validated data
     */
    public function saveMaintenance(Asset $asset, AssetMaintenance $maintenance, array $data, int $userId): AssetMaintenance
    {
        return DB::transaction(function () use ($asset, $maintenance, $data, $userId): AssetMaintenance {
            $asset = Asset::query()->lockForUpdate()->findOrFail($asset->id);
            $maintenance->fill(Arr::only($data, ['type', 'date', 'description', 'cost', 'currency', 'supplier_id', 'odometer_km', 'usage_hours', 'next_due_at']));
            $maintenance->asset_id = $asset->id;
            $maintenance->created_by ??= $userId;

            if (empty($data['currency']) && isset($data['cost'])) {
                $maintenance->currency = $asset->company()->value('currency');
            }
            $maintenance->save();

            $this->raiseUsageReadings($asset, $maintenance);
            $this->refreshNextMaintenance($asset);

            return $maintenance;
        });
    }

    public function deleteMaintenance(AssetMaintenance $maintenance): void
    {
        DB::transaction(function () use ($maintenance): void {
            $asset = Asset::query()->lockForUpdate()->findOrFail($maintenance->asset_id);
            $maintenance->delete();
            $this->refreshNextMaintenance($asset);
        });
    }

    /**
     * Readings only move forward: a maintenance with a lower reading does not lower the vehicle's.
     */
    private function raiseUsageReadings(Asset $asset, AssetMaintenance $maintenance): void
    {
        $vehicle = AssetVehicle::query()->whereKey($asset->id)->lockForUpdate()->first();

        if ($vehicle === null) {
            return;
        }

        foreach (['odometer_km', 'usage_hours'] as $reading) {
            if ($maintenance->{$reading} !== null && $maintenance->{$reading} > (int) $vehicle->{$reading}) {
                $vehicle->{$reading} = $maintenance->{$reading};
            }
        }

        $vehicle->save();
    }

    /**
     * The next maintenance of the asset is the next due date of its most recent record.
     */
    public function refreshNextMaintenance(Asset $asset): void
    {
        $latest = $asset->maintenances()->orderByDesc('date')->orderByDesc('id')->first();

        $asset->next_maintenance_at = $latest?->next_due_at;
        $asset->save();
    }
}
