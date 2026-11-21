@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', ['title' => $carrier->exists ? __('Edit carrier') : __('New carrier')])

    <form method="post" action="{{ url('/carrier/save') }}" class="form">
        @csrf
        <input type="hidden" name="id" value="{{ $carrier->id }}">

        <div class="card mb-2">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="name" class="label-control">{{ __('Name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $carrier->name) }}" maxlength="100" required
                               class="form-control form-control-lg @error('name') is-invalid @enderror">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 form-group">
                        @include('components.country', ['country_id' => old('country_id', $carrier->country_id), 'class' => 'form-select-lg'.($errors->has('country_id') ? ' is-invalid' : '')])
                        @error('country_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-2 form-group">
                        <label for="phone" class="label-control">{{ __('Phone') }}</label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone', $carrier->phone) }}" maxlength="30"
                               class="form-control form-control-lg @error('phone') is-invalid @enderror">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col form-group">
                        <label for="website" class="label-control">{{ __('Website') }}</label>
                        <input type="url" name="website" id="website" value="{{ old('website', $carrier->website) }}" maxlength="255"
                               class="form-control form-control-lg @error('website') is-invalid @enderror">
                        @error('website')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <a href="{{ url('/carrier') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
        </div>
    </form>
@endsection
