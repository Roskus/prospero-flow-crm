@php($device = $asset->device)
@php($phoneLineTypes = collect(\App\Enums\AssetType::cases())->filter->hasPhoneLine()->map->value->values())
<div class="card mb-2" id="phone-line" data-types='@json($phoneLineTypes)'>
    <div class="card-header"><span class="card-title">{{ __('Phone line') }}</span></div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 form-group mb-2">
                <label for="carrier_id" class="label-control">{{ __('Carrier') }}</label>
                <select name="carrier_id" id="carrier_id" class="form-select @error('carrier_id') is-invalid @enderror">
                    <option value=""></option>
                    @php($selectedCarrier = (int) old('carrier_id', $device?->carrier_id))
                    @foreach($carriers as $carrier)
                        <option value="{{ $carrier->id }}" @selected($selectedCarrier === $carrier->id)>{{ $carrier->name }}</option>
                    @endforeach
                    @if($device?->carrier?->trashed())
                        <option value="{{ $device->carrier_id }}" @selected($selectedCarrier === $device->carrier_id)>{{ $device->carrier->name }} ({{ __('deleted') }})</option>
                    @endif
                </select>
                @error('carrier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                @can('update asset')
                    <small class="form-text"><a href="{{ url('/carrier') }}" target="_blank">{{ __('Manage carriers') }}</a></small>
                @endcan
            </div>
            @include('asset._field', ['name' => 'phone_number', 'label' => __('Mobile number'), 'type' => 'tel', 'value' => $device?->phone_number, 'col' => 'col-md-6', 'attributes' => 'maxlength="30"'])
        </div>
        <div class="row">
            @include('asset._field', ['name' => 'sim_pin', 'label' => __('SIM PIN'), 'value' => $device?->sim_pin, 'col' => 'col-md-3', 'attributes' => 'maxlength="8" inputmode="numeric" autocomplete="off"'])
            @include('asset._field', ['name' => 'sim_puk', 'label' => __('SIM PUK'), 'value' => $device?->sim_puk, 'col' => 'col-md-3', 'attributes' => 'maxlength="8" inputmode="numeric" autocomplete="off"'])
        </div>
    </div>
</div>

<div class="card mb-2">
    <div class="card-header"><span class="card-title">{{ __('Device data') }}</span></div>
    <div class="card-body">
        <div class="row">
            @include('asset._field', ['name' => 'hostname', 'label' => __('Hostname'), 'value' => $device?->hostname, 'col' => 'col-md-3', 'attributes' => 'maxlength="100"'])
            @include('asset._field', ['name' => 'ip_address', 'label' => __('IP address'), 'value' => $device?->ip_address, 'col' => 'col-md-3', 'attributes' => 'maxlength="45"'])
            @include('asset._field', ['name' => 'mac_address', 'label' => __('MAC address'), 'value' => $device?->mac_address, 'col' => 'col-md-3', 'attributes' => 'maxlength="17" placeholder="00:1A:2B:3C:4D:5E"'])
            @include('asset._field', ['name' => 'operating_system', 'label' => __('Operating system'), 'value' => $device?->operating_system, 'col' => 'col-md-3', 'attributes' => 'maxlength="100"'])
        </div>
        <div class="row">
            @include('asset._field', ['name' => 'cpu', 'label' => __('CPU'), 'value' => $device?->cpu, 'col' => 'col-md-4', 'attributes' => 'maxlength="100"'])
            @include('asset._field', ['name' => 'ram', 'label' => __('RAM'), 'value' => $device?->ram, 'col' => 'col-md-4', 'attributes' => 'maxlength="50"'])
            @include('asset._field', ['name' => 'storage', 'label' => __('Storage'), 'value' => $device?->storage, 'col' => 'col-md-4', 'attributes' => 'maxlength="100"'])
        </div>
        <div class="form-group">
            <label for="specs" class="label-control">{{ __('Other specs') }}</label>
            <textarea name="specs" id="specs" rows="2" maxlength="5000" class="form-control @error('specs') is-invalid @enderror">{{ old('specs', $device?->specs) }}</textarea>
            @error('specs')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        const typeSelect = document.getElementById('type');
        const phoneLine = document.getElementById('phone-line');
        const phoneLineTypes = JSON.parse(phoneLine.dataset.types);
        const imeiField = document.getElementById('imei-field');
        const imei = document.getElementById('imei');

        function togglePhoneLine() {
            const hasLine = phoneLineTypes.includes(typeSelect.value);
            phoneLine.classList.toggle('d-none', !hasLine);
            phoneLine.querySelectorAll('input, select').forEach((field) => field.disabled = !hasLine);
            imeiField.classList.toggle('d-none', !hasLine);
            imei.disabled = !hasLine;
            imei.required = typeSelect.value === 'mobile';
        }

        typeSelect.addEventListener('change', togglePhoneLine);
        togglePhoneLine();
    })();
</script>
@endpush
