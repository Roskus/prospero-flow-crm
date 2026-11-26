@extends('layouts.app')

@section('content')
    @php($category = $asset->category)
    @include('layouts.partials._header', ['title' => ($asset->exists ? __('Edit') : __('New')).' · '.$category->label()])

    <form method="post" action="{{ url('/inventory/save') }}" enctype="multipart/form-data" class="form">
        @csrf
        <input type="hidden" name="id" value="{{ $asset->id }}">
        <input type="hidden" name="category" value="{{ $category->value }}">

        <div class="card mb-2">
            <div class="card-header"><span class="card-title">{{ __('Details') }}</span></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 form-group mb-2">
                        <label for="type" class="label-control">{{ __('Type') }} <span class="text-danger">*</span></label>
                        <select name="type" id="type" required class="form-select @error('type') is-invalid @enderror">
                            <option value=""></option>
                            @foreach(\App\Enums\AssetType::forCategory($category) as $type)
                                <option value="{{ $type->value }}" @selected(old('type', $asset->type?->value) === $type->value)>{{ $type->label() }}</option>
                            @endforeach
                        </select>
                        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3 form-group mb-2">
                        <label for="code" class="label-control">{{ __('Code') }} <span class="text-danger">*</span></label>
                        <div class="input-group has-validation">
                            <input type="text" name="code" id="code" value="{{ old('code', $asset->code) }}" maxlength="50" required pattern="[A-Za-z0-9\-]+" @readonly($asset->exists)
                                   class="form-control text-uppercase font-monospace @error('code') is-invalid @enderror">
                            @unless($asset->exists)
                            <button type="button" id="generate-code" class="btn btn-outline-secondary" data-url="{{ url('/inventory/code/next') }}" title="{{ __('Generate code') }}">
                                <i class="las la-magic"></i> {{ __('Generate') }}
                            </button>
                            @endunless
                            @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <small class="form-text text-muted">{{ $asset->exists ? __('Fixed: it may be printed on labels.') : __('Letters, numbers and hyphens; printed as barcode and QR. It cannot be changed later.') }}</small>
                    </div>
                    @include('asset._field', ['name' => 'name', 'label' => __('Name'), 'value' => $asset->name, 'required' => true, 'col' => 'col-md-3', 'attributes' => 'maxlength="255"'])
                    <div class="col-md-3 form-group mb-2">
                        <label for="status" class="label-control">{{ __('Status') }}</label>
                        @php($selectedStatus = old('status', $asset->status?->value ?? \App\Enums\AssetStatus::Available->value))
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                            @foreach(\App\Enums\AssetStatus::cases() as $status)
                                <option value="{{ $status->value }}" @selected($selectedStatus === $status->value)>{{ $status->label() }}</option>
                            @endforeach
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="row" id="assigned-user">
                    @php($holder = $asset->currentAssignment?->user)
                    <div class="col-md-4 offset-md-8 form-group mb-2">
                        <label for="assigned_user_id" class="label-control">{{ __('Assigned to') }} <span class="text-danger">*</span></label>
                        @php($selectedHolder = (int) old('assigned_user_id', $holder?->id))
                        <select name="assigned_user_id" id="assigned_user_id" class="form-select @error('assigned_user_id') is-invalid @enderror">
                            <option value=""></option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" @selected($selectedHolder === $user->id)>{{ $user->getFullName() }}</option>
                            @endforeach
                            @if($holder !== null && ! $users->contains('id', $holder->id))
                                <option value="{{ $holder->id }}" @selected($selectedHolder === $holder->id)>{{ $holder->getFullName() }} ({{ __('inactive') }})</option>
                            @endif
                        </select>
                        @error('assigned_user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @if($holder !== null)
                            <small class="form-text text-muted">{{ __('Since :date. Changing the user or the status closes this assignment.', ['date' => $asset->currentAssignment->start_date->format('d/m/Y')]) }}</small>
                        @endif
                    </div>
                </div>
                <div class="row">
                    @php($isDevice = $category === \App\Enums\AssetCategory::Device)
                    @include('asset._field', ['name' => 'brand', 'label' => __('Brand'), 'value' => $asset->brand, 'col' => $isDevice ? 'col-md-3' : 'col-md-4', 'attributes' => 'maxlength="100"'])
                    @include('asset._field', ['name' => 'model', 'label' => __('Model'), 'value' => $asset->model, 'col' => $isDevice ? 'col-md-3' : 'col-md-4', 'attributes' => 'maxlength="100"'])
                    @include('asset._field', ['name' => 'serial_number', 'label' => __('Serial number'), 'value' => $asset->serial_number, 'col' => $isDevice ? 'col-md-3' : 'col-md-4', 'attributes' => 'maxlength="100"'])
                    @if($isDevice)
                        <div class="col-md-3" id="imei-field">
                            <div class="row">
                                @include('asset._field', ['name' => 'imei', 'label' => __('IMEI'), 'value' => $asset->device?->imei, 'col' => 'col-12', 'attributes' => 'maxlength="17" inputmode="numeric"'])
                            </div>
                        </div>
                    @endif
                </div>
                <div class="row">
                    <div class="col-md-4 form-group mb-2">
                        <label for="branch_id" class="label-control">{{ __('Branch') }}</label>
                        <select name="branch_id" id="branch_id" class="form-select @error('branch_id') is-invalid @enderror">
                            <option value=""></option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" @selected((int) old('branch_id', $asset->branch_id) === $branch->id)>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        @error('branch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    @include('asset._field', ['name' => 'warranty_until', 'label' => __('Warranty until'), 'type' => 'date', 'value' => $asset->warranty_until?->toDateString(), 'col' => 'col-md-4'])
                </div>
            </div>
        </div>

        <div class="card mb-2">
            <div class="card-header"><span class="card-title">{{ __('Acquisition') }}</span></div>
            <div class="card-body">
                @php($acquisition = old('acquisition_type', $asset->acquisition_type?->value ?? \App\Enums\AcquisitionType::Purchase->value))
                <div class="mb-3">
                    @foreach(\App\Enums\AcquisitionType::cases() as $acquisitionType)
                        <div class="form-check form-check-inline">
                            <input type="radio" name="acquisition_type" id="acquisition_{{ $acquisitionType->value }}" value="{{ $acquisitionType->value }}" class="form-check-input" @checked($acquisition === $acquisitionType->value)>
                            <label for="acquisition_{{ $acquisitionType->value }}" class="form-check-label">{{ $acquisitionType->label() }}</label>
                        </div>
                    @endforeach
                    @error('acquisition_type')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>
                <div class="row">
                    <div class="col-md-4 form-group mb-2">
                        <label for="supplier_id" class="label-control">
                            <span data-acquisition="purchase">{{ __('Supplier') }}</span>
                            <span data-acquisition="renting">{{ __('Renting company') }}</span>
                        </label>
                        <select name="supplier_id" id="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror">
                            <option value=""></option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" @selected((int) old('supplier_id', $asset->supplier_id) === $supplier->id)>{{ $supplier->name }}@if(isset($supplier->deleted_at)) ({{ __('deleted') }})@endif</option>
                            @endforeach
                        </select>
                        @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 form-group mb-2">
                        @include('components.currency', ['currency' => old('currency', $asset->currency ?? Auth::user()->company->currency)])
                        @error('currency')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="row" data-acquisition="purchase">
                    @include('asset._field', ['name' => 'purchase_date', 'label' => __('Purchase date'), 'type' => 'date', 'value' => $asset->purchase_date?->toDateString(), 'col' => 'col-md-4'])
                    @include('asset._field', ['name' => 'purchase_price', 'label' => __('Purchase price'), 'type' => 'number', 'value' => $asset->purchase_price, 'col' => 'col-md-4', 'attributes' => 'min="0" step="0.01"'])
                </div>
                <div class="row" data-acquisition="renting">
                    @include('asset._field', ['name' => 'rental_monthly_fee', 'label' => __('Monthly fee'), 'type' => 'number', 'value' => $asset->rental_monthly_fee, 'col' => 'col-md-4', 'attributes' => 'min="0" step="0.01"'])
                    @include('asset._field', ['name' => 'rental_starts_at', 'label' => __('Contract start'), 'type' => 'date', 'value' => $asset->rental_starts_at?->toDateString(), 'col' => 'col-md-4'])
                    @include('asset._field', ['name' => 'rental_ends_at', 'label' => __('Contract end'), 'type' => 'date', 'value' => $asset->rental_ends_at?->toDateString(), 'col' => 'col-md-4'])
                </div>
            </div>
        </div>

        <div class="card mb-2">
            <div class="card-header"><span class="card-title">{{ __('Photo') }}</span></div>
            <div class="card-body">
                <div class="d-flex align-items-start gap-3">
                    @include('asset._thumbnail', ['asset' => $asset, 'size' => 120])
                    <div class="flex-grow-1">
                        <label for="photo" class="visually-hidden">{{ __('Photo') }}</label>
                        <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/webp" class="form-control @error('photo') is-invalid @enderror">
                        @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="form-text text-muted">{{ __('JPG, PNG or WebP, up to 5 MB.') }}</small>
                        @if($asset->hasPhoto())
                            <div class="form-check mt-2">
                                <input type="checkbox" name="remove_photo" id="remove_photo" value="1" class="form-check-input">
                                <label for="remove_photo" class="form-check-label">{{ __('Remove photo') }}</label>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @includeWhen($category === \App\Enums\AssetCategory::Vehicle, 'asset._vehicle')
        @includeWhen($category === \App\Enums\AssetCategory::Vehicle, 'asset._machinery')
        @includeWhen($category === \App\Enums\AssetCategory::Device, 'asset._device')

        <div class="card mb-2">
            <div class="card-body">
                <label for="notes" class="label-control">{{ __('Notes') }}</label>
                <textarea name="notes" id="notes" rows="3" maxlength="5000" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $asset->notes) }}</textarea>
                @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="mb-3">
            <a href="{{ $asset->exists ? route('asset.show', $asset->id) : route('asset.index', $category->value) }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    (function () {
        const status = document.getElementById('status');
        const assignedUser = document.getElementById('assigned-user');
        const holder = document.getElementById('assigned_user_id');

        function toggleAssignedUser() {
            const isAssigned = status.value === 'assigned';
            assignedUser.classList.toggle('d-none', !isAssigned);
            holder.disabled = !isAssigned;
            holder.required = isAssigned;
        }

        function toggleAcquisition() {
            const selected = document.querySelector('input[name="acquisition_type"]:checked')?.value ?? 'purchase';
            document.querySelectorAll('[data-acquisition]').forEach((element) => {
                const visible = element.dataset.acquisition === selected;
                element.classList.toggle('d-none', !visible);
                element.querySelectorAll('input, select').forEach((field) => field.disabled = !visible);
            });
        }

        const generateCode = document.getElementById('generate-code');
        generateCode?.addEventListener('click', async () => {
            const type = document.getElementById('type');
            if (!type.value) {
                type.reportValidity();
                return;
            }
            generateCode.disabled = true;
            try {
                const response = await fetch(generateCode.dataset.url + '?type=' + encodeURIComponent(type.value), {headers: {'Accept': 'application/json'}});
                if (response.ok) {
                    document.getElementById('code').value = (await response.json()).code;
                }
            } finally {
                generateCode.disabled = false;
            }
        });

        status.addEventListener('change', toggleAssignedUser);
        document.querySelectorAll('input[name="acquisition_type"]').forEach((radio) => radio.addEventListener('change', toggleAcquisition));
        toggleAssignedUser();
        toggleAcquisition();
    })();
</script>
@endpush
