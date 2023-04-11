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
                    <label for="name">{{ __('Name') }} <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" value="{{ $supplier->name }}" required="required" class="form-control">
                </div>
                <div class="col">
                    <label for="business_name">{{ __('Business name') }}</label>
                    <input type="text" name="business_name" id="business_name" value="{{ $supplier->business_name }}" class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="phone">{{ __('Phone') }}</label>
                    <input type="tel" name="phone" id="phone" value="{{ $supplier->phone }}" maxlength="15" class="form-control">
                </div>
                <div class="col">
                    <label for="vat" class="">{{ __('Identity number') }}</label>
                    <input type="text" name="vat" id="vat" value="{{ $supplier->vat }}" maxlength="20" class="form-control form-control-lg">
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="email">E-mail</label>
                    <input type="email" name="email" id="email" value="{{ $supplier->email }}" maxlength="254" class="form-control">
                </div>
                <div class="col">
                    <label for="website">{{ __('Website') }}</label>
                    <input type="url" name="website" id="website" value="{{ $supplier->website }}" maxlength="255" class="form-control">
                </div>
            </div>
            <div class="card mt-2">
                <div class="card-header">
                    {{ __('Address') }}
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <label for="country_id">{{ __('Country') }}</label>
                            <input name="country_id" id="country_id" list="countryOptions"
                                   value="{{ $supplier->country_id }}" placeholder="{{ __('Type to search...') }}"
                                   autocomplete="off"  class="form-control form-control-lg">
                            <datalist id="countryOptions">
                                <option value=""></option>
                                @foreach ($countries as $country)
                                <option value="{{ $country->code_2 }}"
                                @if((!empty($supplier->country_id)) && $supplier->country_id == $country->code_2) selected="selected" @endif>
                                {{ $country->name }} {{ $country->flag }}
                                </option>
                                @endforeach
                            </datalist>
                        </div>
                        <div class="col">
                            <label for="province">{{ __('Province') }}</label>
                            <input type="text" name="province" id="province" value="{{ old('province', $supplier->province) }}" class="form-control form-control-lg">
                        </div>
                    </div><!--./row-->
                    <div class="row">
                        <div class="col">
                            <label for="street">{{ __('Street') }}</label>
                            <input type="text" name="street" id="street" value="{{ old('street', $supplier->street) }}" class="form-control form-control-lg">
                        </div>
                        <div class="col">
                            <label for="zipcode">{{ __('Zipcode') }}</label>
                            <input type="text" name="zipcode" id="zipcode" value="{{ old('zipcode', $supplier->zipcode) }}" maxlength="10" class="form-control form-control-lg">
                        </div>
                    </div><!--./row-->
                </div><!--./card-body-->
            </div><!--./card-->
            <div class="row">
                <div class="col mt-2">
                    <a href="{{ url('supplier') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                </div>
            </div>
            <input type="hidden" name="id" value="{{ $supplier->id }}">
            </form>
        </div>
    </div>
@endsection
