@php($vehicle = $asset->vehicle)
<div class="card mb-2">
    <div class="card-header"><span class="card-title">{{ __('Vehicle data') }}</span></div>
    <div class="card-body">
        <div class="row">
            @include('asset._field', ['name' => 'license_plate', 'label' => __('License plate'), 'value' => $vehicle?->license_plate, 'col' => 'col-md-3', 'attributes' => 'maxlength="20"'])
            @include('asset._field', ['name' => 'vin', 'label' => __('VIN / chassis number'), 'value' => $vehicle?->vin, 'col' => 'col-md-5', 'attributes' => 'maxlength="50"'])
            @include('asset._field', ['name' => 'manufacturing_year', 'label' => __('Year'), 'type' => 'number', 'value' => $vehicle?->manufacturing_year, 'col' => 'col-md-2', 'attributes' => 'min="1900" max="'.(now()->year + 1).'"'])
            <div class="col-md-2 form-group mb-2">
                <label for="fuel_type" class="label-control">{{ __('Fuel') }}</label>
                <select name="fuel_type" id="fuel_type" class="form-select @error('fuel_type') is-invalid @enderror">
                    <option value=""></option>
                    @foreach(\App\Enums\FuelType::cases() as $fuel)
                        <option value="{{ $fuel->value }}" @selected(old('fuel_type', $vehicle?->fuel_type?->value) === $fuel->value)>{{ $fuel->label() }}</option>
                    @endforeach
                </select>
                @error('fuel_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="row">
            @include('asset._field', ['name' => 'odometer_km', 'label' => __('Odometer (km)'), 'type' => 'number', 'value' => $vehicle?->odometer_km, 'col' => 'col-md-3', 'attributes' => 'min="0"'])
            @include('asset._field', ['name' => 'usage_hours', 'label' => __('Usage hours'), 'type' => 'number', 'value' => $vehicle?->usage_hours, 'col' => 'col-md-3', 'attributes' => 'min="0"'])
            @include('asset._field', ['name' => 'inspection_expires_at', 'label' => __('Technical inspection expires'), 'type' => 'date', 'value' => $vehicle?->inspection_expires_at?->toDateString(), 'col' => 'col-md-3'])
        </div>
        <div class="row">
            @include('asset._field', ['name' => 'insurance_company', 'label' => __('Insurance company'), 'value' => $vehicle?->insurance_company, 'col' => 'col-md-4', 'attributes' => 'maxlength="100"'])
            @include('asset._field', ['name' => 'insurance_policy', 'label' => __('Insurance policy'), 'value' => $vehicle?->insurance_policy, 'col' => 'col-md-4', 'attributes' => 'maxlength="100"'])
            @include('asset._field', ['name' => 'insurance_expires_at', 'label' => __('Insurance expires'), 'type' => 'date', 'value' => $vehicle?->insurance_expires_at?->toDateString(), 'col' => 'col-md-3'])
        </div>
    </div>
</div>
