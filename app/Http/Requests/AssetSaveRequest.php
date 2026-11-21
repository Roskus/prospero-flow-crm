<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\AcquisitionType;
use App\Enums\AssetCategory;
use App\Enums\AssetStatus;
use App\Enums\AssetType;
use App\Enums\FuelType;
use App\Http\Requests\Concerns\ValidatesCurrency;
use App\Models\Asset;
use App\Models\AssetDevice;
use App\Models\User;
use App\Rules\ExtensionMatchesContent;
use App\Services\AssetCodeService;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AssetSaveRequest extends FormRequest
{
    use ValidatesCurrency {
        prepareForValidation as prepareCurrency;
    }

    const string MAC_ADDRESS_PATTERN = '/^([0-9A-Fa-f]{2}[:-]){5}[0-9A-Fa-f]{2}$/';

    private ?Asset $editedAsset = null;

    public function authorize(): bool
    {
        return Auth::user()?->can($this->filled('id') ? 'update asset' : 'create asset') ?? false;
    }

    /**
     * The category of an existing asset cannot change, so it is taken from the asset.
     */
    protected function prepareForValidation(): void
    {
        $this->prepareCurrency();

        if ($this->filled('id')) {
            $this->editedAsset = Asset::ofCompany($this->companyId())->with('vehicle', 'device', 'currentAssignment')->findOrFail($this->integer('id'));
            $this->merge(['category' => $this->editedAsset->category->value]);
        }

        $this->merge(array_filter([
            'code' => is_string($this->input('code')) ? strtoupper(trim($this->input('code'))) : null,
            'license_plate' => is_string($this->input('license_plate')) ? strtoupper(str_replace([' ', '-'], '', $this->input('license_plate'))) : null,
            'phone_number' => is_string($this->input('phone_number')) ? str_replace([' ', '-', '.', '(', ')'], '', $this->input('phone_number')) : null,
            'imei' => is_string($this->input('imei')) ? str_replace([' ', '-'], '', $this->input('imei')) : null,
            'sim_pin' => is_string($this->input('sim_pin')) ? str_replace(' ', '', $this->input('sim_pin')) : null,
            'sim_puk' => is_string($this->input('sim_puk')) ? str_replace(' ', '', $this->input('sim_puk')) : null,
        ], fn (?string $value): bool => $value !== null));
    }

    public function editedAsset(): ?Asset
    {
        return $this->editedAsset;
    }

    public function assetCategory(): ?AssetCategory
    {
        return AssetCategory::tryFrom((string) $this->input('category'));
    }

    public function assetType(): ?AssetType
    {
        $type = AssetType::tryFrom((string) $this->input('type'));

        return $type !== null && $type->category() === $this->assetCategory() ? $type : null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $category = $this->assetCategory();

        $rules = [
            'id' => ['nullable', 'integer'],
            'category' => ['required', Rule::enum(AssetCategory::class)],
            'type' => [
                'required',
                Rule::in(array_map(fn (AssetType $type): string => $type->value, $category ? AssetType::forCategory($category) : [])),
            ],
            'status' => ['nullable', Rule::enum(AssetStatus::class)],
            'assigned_user_id' => ['nullable', 'required_if:status,'.AssetStatus::Assigned->value, 'integer', $this->assignableUserRule()],
            'code' => [
                'required',
                'string',
                'max:50',
                'regex:'.AssetCodeService::CODE_PATTERN,
                Rule::unique('asset', 'code')
                    ->where('company_id', $this->companyId())
                    ->whereNull('deleted_at')
                    ->ignore($this->editedAsset?->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],
            'serial_number' => ['nullable', 'string', 'max:100'],
            'branch_id' => ['nullable', 'integer', Rule::exists('branch', 'id')->where('company_id', $this->companyId())->whereNull('deleted_at')],
            'supplier_id' => ['nullable', 'integer', Rule::exists('supplier', 'id')->where('company_id', $this->companyId())->whereNull('deleted_at')],
            'acquisition_type' => ['nullable', Rule::enum(AcquisitionType::class)],
            'purchase_date' => ['nullable', 'date'],
            'purchase_price' => ['nullable', 'numeric', 'min:0', 'max:9999999999'],
            'rental_monthly_fee' => ['nullable', 'numeric', 'min:0', 'max:9999999999'],
            'rental_starts_at' => ['nullable', 'date'],
            'rental_ends_at' => ['nullable', 'date', 'after_or_equal:rental_starts_at'],
            'currency' => $this->currencyRules(),
            'warranty_until' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120', new ExtensionMatchesContent],
            'remove_photo' => ['nullable', 'boolean'],
        ];

        return match ($category) {
            AssetCategory::Vehicle => [...$rules, ...$this->vehicleRules()],
            AssetCategory::Device => [...$rules, ...$this->deviceRules()],
            default => $rules,
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function vehicleRules(): array
    {
        return [
            'license_plate' => ['nullable', 'string', 'max:20'],
            'vin' => ['nullable', 'string', 'max:50'],
            'manufacturing_year' => ['nullable', 'integer', 'min:1900', 'max:'.(now()->year + 1)],
            'fuel_type' => ['nullable', Rule::enum(FuelType::class)],
            'odometer_km' => ['nullable', 'integer', 'min:0', 'max:4294967295'],
            'usage_hours' => ['nullable', 'integer', 'min:0', 'max:4294967295'],
            'insurance_company' => ['nullable', 'string', 'max:100'],
            'insurance_policy' => ['nullable', 'string', 'max:100'],
            'insurance_expires_at' => ['nullable', 'date'],
            'inspection_expires_at' => ['nullable', 'date'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function deviceRules(): array
    {
        $type = $this->assetType();
        $rules = [
            'hostname' => ['nullable', 'string', 'max:100'],
            'ip_address' => ['nullable', 'ip'],
            'mac_address' => ['nullable', 'string', 'regex:'.self::MAC_ADDRESS_PATTERN],
            'operating_system' => ['nullable', 'string', 'max:100'],
            'cpu' => ['nullable', 'string', 'max:100'],
            'ram' => ['nullable', 'string', 'max:50'],
            'storage' => ['nullable', 'string', 'max:100'],
            'specs' => ['nullable', 'string', 'max:5000'],
        ];

        if ($type?->hasPhoneLine()) {
            $rules['imei'] = [$type->requiresImei() ? 'required' : 'nullable', 'digits:15', $this->uniqueImeiRule()];
            $rules['carrier_id'] = ['nullable', 'integer', Rule::exists('carrier', 'id')->where('company_id', $this->companyId())->whereNull('deleted_at')];
            $rules['phone_number'] = ['nullable', 'string', 'max:30', 'regex:/^\+?[0-9]{3,20}$/'];
            $rules['sim_pin'] = ['nullable', 'digits_between:4,8'];
            $rules['sim_puk'] = ['nullable', 'digits:8'];
        }

        return $rules;
    }

    private function uniqueImeiRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            $exists = AssetDevice::query()
                ->where('imei', $value)
                ->whereHas('asset', fn (Builder $query) => $query->where('company_id', $this->companyId())->whereKeyNot($this->editedAsset->id ?? 0))
                ->exists();

            if ($exists) {
                $fail(__('This IMEI is already registered on another asset.'));
            }
        };
    }

    /**
     * The holder must be an active user of the company; the current holder stays valid even if deactivated.
     */
    private function assignableUserRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            if ((int) $value === $this->editedAsset?->currentAssignment?->user_id) {
                return;
            }

            $isActiveCompanyUser = User::query()
                ->whereKey((int) $value)
                ->where('company_id', $this->companyId())
                ->where('active', true)
                ->exists();

            if (! $isActiveCompanyUser) {
                $fail(__('The selected user is not valid.'));
            }
        };
    }

    private function companyId(): int
    {
        return (int) Auth::user()->company_id;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'type.in' => __('The type is not valid for this category.'),
            'assigned_user_id.required_if' => __('Select the user who receives the asset.'),
            'rental_ends_at.after_or_equal' => __('The renting end date cannot be earlier than its start date.'),
            'code.unique' => __('An asset with this code already exists.'),
            'code.regex' => __('The code can only have letters, numbers and hyphens.'),
            'mac_address.regex' => __('The MAC address format is not valid.'),
            'ip_address.ip' => __('The IP address is not valid.'),
            'imei.required' => __('The IMEI is required for mobile phones.'),
            'imei.digits' => __('The IMEI must have 15 digits.'),
            'phone_number.regex' => __('The mobile number format is not valid.'),
            'sim_pin.digits_between' => __('The PIN must have between 4 and 8 digits.'),
            'sim_puk.digits' => __('The PUK must have 8 digits.'),
            'branch_id.exists' => __('The selected branch is not valid.'),
            'supplier_id.exists' => __('The selected supplier is not valid.'),
            'carrier_id.exists' => __('The selected carrier is not valid.'),
        ];
    }
}
