@extends('layouts.app')

@section('content')
<header>
   <h1>{{ __('Company') }}</h1>
</header>

<div class="card">
    <div class="card-body">
    <form method="post" action="{{ url('/company/save') }}" class="form" enctype="multipart/form-data">
        <div class="row">
            <div class="col">
                <label for="name" class="label-control">{{ __('Name') }} <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $company->name) }}" maxlength="80" required="required" class="form-control">
            </div>
            <div class="col">
                <label for="business_name" class="label-control">{{ __('Business name') }}</label>
                <input type="text" name="business_name" id="business_name" value="{{ old('business_name', $company->business_name) }}" maxlength="80" required="required" class="form-control">
            </div>
        </div>
        <div class="row">
            <div class="col form-group">
                <label for="phone" class="label-control">{{ __('Phone') }}</label>
                <input type="tel" name="phone" id="phone" value="{{ old('phone', $company->phone) }}" maxlength="15"
                       class="form-control">
            </div>

            <div class="col form-group">
                <label for="email" class="label-control">E-mail <span class="text-danger">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email', $company->email) }}" maxlength="254"
                       required="required" class="form-control">
            </div>
        </div>
        <div class="row">
            <div class="col">
                <label for="vat" class="label-control">{{ __('Tax identification') }}</label>
                <input type="text" name="vat" id="vat" value="{{ old('vat', $company->vat) }}" maxlength="20"
                       class="form-control">
            </div>
            <div class="col form-group">
                <label for="currency" class="label-control">{{ __('Currency') }}</label>
                <input type="text" name="currency" id="currency" list="currency-list" value="{{ old('currency', $company->currency) }}"
                       class="form-control" maxlength="3">
                <datalist id="currency-list">
                    @if(!empty($currencies))
                        @foreach($currencies as $currency)
                            <option value="{{ $currency->id }}">{{ $currency->id }} ({{ $currency->symbol }})</option>
                        @endforeach
                    @endif
                </datalist>
            </div>
        </div>
        <div class="row">
            <div class="col form-group">
                <label for="website" class="label-control">{{ __('Website') }}</label>
                <input type="url" name="website" id="website" value="{{ old('website', $company->website) }}"
                       class="form-control">
            </div>
        </div>
        <div class="row">
            <div class="col">
                <label for="country_id" class="label-control">{{ __('Country') }} <span class="text-danger">*</span></label>
                <select name="country_id" id="country_id" required class="form-control">
                    <option value=""></option>
                    @foreach ($countries as $country)
                        <option value="{{ $country->code_2 }}" @if($company->country_id == $country->code_2) selected="selected" @endif>{{ $country->name }} {{ $country->flag }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col">
                <label for="province" class="label-control">{{ __('Province') }}</label>
                <input type="text" name="province" id="province" value="{{ old('province', $company->province) }}"
                       class="form form-control">
            </div>
        </div>
        <div class="row">
            <div class="col form-group">
                <label for="city" class="label-control">{{ __('City') }}</label>
                <input type="text" name="city" id="city" value="{{ old('city', $company->city) }}"
                       class="form form-control">
            </div>
            <div class="col form-group">
                <label for="street" class="label-control">{{ __('Street') }}</label>
                <input type="text" name="street" id="street" value="{{ old('street', $company->street) }}"
                       class="form form-control">
            </div>
        </div>
        <div class="row">
            <div class="col form-group">
                <label for="zipcode" class="label-control">{{ __('Zipcode') }}</label>
                <input type="text" name="zipcode" id="zipcode" value="{{ old('zipcode', $company->zipcode) }}"
                       class="form form-control" maxlength="10">
            </div>
        </div>
        <div class="row">
            <div class="col form-group">
                <label for="logo" class="label-control">Logo</label>
                <input type="file" name="logo" id="logo" accept="image/png, image/gif, image/jpeg, image/svg" class="form-control">
                @if($company->logo)
                    <img src="/asset/upload/company/{{ \Illuminate\Support\Str::slug($company->name, '_') }}/{{ $company->logo }}" alt="{{ env('APP_NAME') }}" class="logo">
                @endif
            </div>
        </div>
        <div class="form-group mt-2">
            <a class="btn btn-secondary" href="{{ url('/company')}}">{{ __('Cancel') }}</a>
            <button type="submit" class="btn btn-primary"><span class=""></span> {{ __('Save') }}</button>
        </div>
        <input type="hidden" name="id" id="id" value="{{ $company->id }}">
        {{ csrf_field() }}
    </form>
    </div><!--./card-body-->
</div><!--./card-->
@endsection
