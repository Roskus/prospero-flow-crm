@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', ['title' => ($maintenance->exists ? __('Edit maintenance') : __('Log maintenance')).' · '.$asset->code])

    <form method="post" action="{{ url('/inventory/maintenance/save') }}" class="form">
        @csrf
        <input type="hidden" name="id" value="{{ $maintenance->id }}">
        <input type="hidden" name="asset_id" value="{{ $asset->id }}">

        <div class="card mb-2">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 form-group mb-2">
                        <label for="type" class="label-control">{{ __('Type') }} <span class="text-danger">*</span></label>
                        <select name="type" id="type" required class="form-select @error('type') is-invalid @enderror">
                            <option value=""></option>
                            @foreach(\App\Enums\MaintenanceType::cases() as $type)
                                <option value="{{ $type->value }}" @selected(old('type', $maintenance->type?->value) === $type->value)>{{ $type->label() }}</option>
                            @endforeach
                        </select>
                        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    @include('asset._field', ['name' => 'date', 'label' => __('Date'), 'type' => 'date', 'value' => $maintenance->date?->toDateString(), 'required' => true, 'col' => 'col-md-3'])
                    @include('asset._field', ['name' => 'next_due_at', 'label' => __('Next due'), 'type' => 'date', 'value' => $maintenance->next_due_at?->toDateString(), 'col' => 'col-md-3'])
                    <div class="col-md-3 form-group mb-2">
                        <label for="supplier_id" class="label-control">{{ __('Supplier') }}</label>
                        <select name="supplier_id" id="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror">
                            <option value=""></option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" @selected((int) old('supplier_id', $maintenance->supplier_id) === $supplier->id)>{{ $supplier->name }}@if(isset($supplier->deleted_at)) ({{ __('deleted') }})@endif</option>
                            @endforeach
                        </select>
                        @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="form-group mb-2">
                    <label for="description" class="label-control">{{ __('Description') }} <span class="text-danger">*</span></label>
                    <textarea name="description" id="description" rows="3" maxlength="2000" required class="form-control @error('description') is-invalid @enderror">{{ old('description', $maintenance->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="row">
                    @include('asset._field', ['name' => 'cost', 'label' => __('Cost'), 'type' => 'number', 'value' => $maintenance->cost, 'col' => 'col-md-3', 'attributes' => 'min="0" step="0.01"'])
                    <div class="col-md-3 form-group mb-2">
                        @include('components.currency', ['currency' => old('currency', $maintenance->currency)])
                        @error('currency')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    @if($asset->isVehicle())
                        @include('asset._field', ['name' => 'odometer_km', 'label' => __('Odometer (km)'), 'type' => 'number', 'value' => $maintenance->odometer_km, 'col' => 'col-md-3', 'attributes' => 'min="0"'])
                        @include('asset._field', ['name' => 'usage_hours', 'label' => __('Usage hours'), 'type' => 'number', 'value' => $maintenance->usage_hours, 'col' => 'col-md-3', 'attributes' => 'min="0"'])
                    @endif
                </div>
            </div>
        </div>

        <div class="mb-3">
            <a href="{{ route('asset.show', $asset->id) }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
        </div>
    </form>
@endsection
