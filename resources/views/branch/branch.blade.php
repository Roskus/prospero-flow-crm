@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', ['title' => $branch->exists ? __('Edit branch') : __('New branch')])

    <form method="post" action="{{ url('/branch/save') }}" class="form">
        @csrf
        <input type="hidden" name="id" value="{{ $branch->id }}">

        <div class="card mb-2">
            <div class="card-header">
                <span class="card-title">{{ __('Details') }}</span>
                @if($branch->is_main)
                    <span class="badge text-bg-success ms-1">{{ __('Main branch') }}</span>
                @endif
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2 form-group">
                        <label for="code" class="label-control">{{ __('Code') }} <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="code" value="{{ old('code', $branch->code) }}" maxlength="10" required
                               class="form-control form-control-lg font-monospace @error('code') is-invalid @enderror">
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">{{ __('It will be the invoice series of the branch.') }}</small>
                    </div>
                    <div class="col form-group">
                        <label for="name" class="label-control">{{ __('Name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $branch->name) }}" maxlength="255" required
                               class="form-control form-control-lg @error('name') is-invalid @enderror">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col form-group">
                        <label for="phone" class="label-control">{{ __('Phone') }}</label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone', $branch->phone) }}" maxlength="30"
                               class="form-control form-control-lg @error('phone') is-invalid @enderror">
                    </div>
                    <div class="col form-group">
                        <label for="email" class="label-control">E-mail</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $branch->email) }}" maxlength="191"
                               class="form-control form-control-lg @error('email') is-invalid @enderror">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-2">
            <div class="card-header">
                <span class="card-title">{{ __('Address') }}</span>
            </div>
            <div class="card-body">
                @include('components.address.form', ['address' => $branch->address, 'class' => 'form-control-lg'])
            </div>
        </div>

        <div class="mb-3">
            <a href="{{ url('/branch') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
        </div>
    </form>
@endsection
