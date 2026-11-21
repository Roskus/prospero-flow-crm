<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AcquisitionType;
use App\Enums\AssetCategory;
use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetMaintenance;
use App\Models\Branch;
use App\Models\Carrier;
use App\Models\Supplier;
use App\Models\User;
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
            'branches' => Branch::ofCompany($company->id)->orderBy('name')->get(['id', 'name']),
            'suppliers' => Supplier::where('company_id', $company->id)->orderBy('name')->get(['id', 'name']),
            'carriers' => Carrier::ofCompany($company->id)->orderBy('name')->get(['id', 'name']),
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
        $oldPhotoPath = $asset->photo_path;
        $newPhotoPath = $photo?->store('asset/'.$companyId, self::PHOTO_DISK);

        try {
            DB::transaction(function () use ($asset, $data, $companyId, $newPhotoPath): void {
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

        if ($current !== null) {
            $current->end_date = $current->start_date->max(today());
            $current->save();
        }

        if ($holderId !== null) {
            $asset->assignments()->create([
                'user_id' => $holderId,
                'assigned_by' => Auth::id(),
                'start_date' => today(),
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

            return;
        }

        $detail = Arr::only(array_merge(array_fill_keys(self::DEVICE_FIELDS, null), $data), self::DEVICE_FIELDS);

        if (! $asset->type->hasPhoneLine()) {
            $detail = array_merge($detail, ['imei' => null, 'carrier_id' => null, 'phone_number' => null, 'sim_pin' => null, 'sim_puk' => null]);
        }

        $asset->device()->updateOrCreate([], $detail);
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

            $assignment = $asset->assignments()->create([
                'user_id' => $userId,
                'assigned_by' => $assignedBy,
                'start_date' => $startDate ?? today()->toDateString(),
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
     * @param  array<string, mixed>  $data  validated data
     */
    public function saveMaintenance(Asset $asset, AssetMaintenance $maintenance, array $data, int $userId): AssetMaintenance
    {
        return DB::transaction(function () use ($asset, $maintenance, $data, $userId): AssetMaintenance {
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
            $asset = $maintenance->asset;
            $maintenance->delete();
            $this->refreshNextMaintenance($asset);
        });
    }

    /**
     * Readings only move forward: a maintenance with a lower reading does not lower the vehicle's.
     */
    private function raiseUsageReadings(Asset $asset, AssetMaintenance $maintenance): void
    {
        $vehicle = $asset->vehicle;

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
