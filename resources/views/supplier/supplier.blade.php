@extends('layouts.app')

@section('content')
    <header>
        <h1>{{ __('Supplier') }}</h1>
    </header>
    <div class="card">
        <div class="card-body">
            <form method="post" action="{{ url('/supplier/save') }}">
            @csrf
            <div class="row">
                <div class="col">
                    <label>{{ __('Name') }} <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ $supplier->name }}" required="required" class="form-control">
                </div>
                <div class="col">
                    <label>{{ __('Business name') }}</label>
                    <input type="text" name="business_name" value="{{ $supplier->business_name }}" class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label>{{ __('Phone') }}</label>
                    <input type="tel" name="phone" value="{{ $supplier->phone }}" maxlength="15" class="form-control">
                </div>
                <div class="col">
                    <label for="vat" class="">{{ __('Identity number') }}</label>
                    <input type="text" name="vat" value="{{ $supplier->vat }}" maxlength="20" class="form-control form-control-lg">
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label>E-mail</label>
                    <input type="email" name="email" value="{{ $supplier->email }}" maxlength="254" class="form-control">
                </div>
                <div class="col">
                    <label>{{ __('Website') }}</label>
                    <input type="url" name="website" value="{{ $supplier->website }}" maxlength="255" class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="country_id">{{ __('Country') }}</label>
                    <select name="country_id" id="country_id" class="form-select">
                        <option value=""></option>
                        @foreach ($countries as $country)
                            <option value="{{ $country->code_2 }}" @if((!empty($supplier->country_id)) && $supplier->country_id == $country->code_2) selected="selected" @endif>{{ $country->name }} {{ $country->flag }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col mt-2">
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                </div>
            </div>
            <input type="hidden" name="id" value="{{ $supplier->id }}">
            </form>
        </div>
    </div>
@endsection
