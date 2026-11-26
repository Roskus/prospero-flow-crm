@php($machinery = $asset->machinery)
@php($machineryTypes = collect(\App\Enums\AssetType::machinery())->map->value->values())
<div class="card mb-2" id="machinery-data" data-types='@json($machineryTypes)'>
    <div class="card-header"><span class="card-title">{{ __('Machinery data') }}</span></div>
    <div class="card-body">
        <div class="row">
            @include('asset._field', ['name' => 'capacity', 'label' => __('Capacity'), 'value' => $machinery?->capacity, 'col' => 'col-md-3', 'attributes' => 'maxlength="50" placeholder="'.__('e.g. 25 t, 100 kVA').'"'])
            @include('asset._field', ['name' => 'certification_number', 'label' => __('Certification number'), 'value' => $machinery?->certification_number, 'col' => 'col-md-3', 'attributes' => 'maxlength="100"'])
            @include('asset._field', ['name' => 'certified_by', 'label' => __('Certified by'), 'value' => $machinery?->certified_by, 'col' => 'col-md-3', 'attributes' => 'maxlength="100"'])
            @include('asset._field', ['name' => 'certification_expires_at', 'label' => __('Certification expires'), 'type' => 'date', 'value' => $machinery?->certification_expires_at?->toDateString(), 'col' => 'col-md-3'])
        </div>
        <div class="row">
            @include('asset._field', ['name' => 'calibration_expires_at', 'label' => __('Calibration expires'), 'type' => 'date', 'value' => $machinery?->calibration_expires_at?->toDateString(), 'col' => 'col-md-3'])
            @include('asset._field', ['name' => 'safety_inspection_expires_at', 'label' => __('Safety inspection expires'), 'type' => 'date', 'value' => $machinery?->safety_inspection_expires_at?->toDateString(), 'col' => 'col-md-3'])
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        const typeSelect = document.getElementById('type');
        const machinery = document.getElementById('machinery-data');
        const machineryTypes = JSON.parse(machinery.dataset.types);

        function toggleMachinery() {
            const isMachinery = machineryTypes.includes(typeSelect.value);
            machinery.classList.toggle('d-none', !isMachinery);
            machinery.querySelectorAll('input, select').forEach((field) => field.disabled = !isMachinery);
        }

        typeSelect.addEventListener('change', toggleMachinery);
        toggleMachinery();
    })();
</script>
@endpush
