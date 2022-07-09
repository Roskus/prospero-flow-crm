@extends('layouts.app')

@section('content')
    <header>
        <h1>{{ __('Leads') }}
    </header>
    <form method="POST" action="/lead/save" class="form">
        {{ csrf_field() }}
        <div class="row">
            <div class="col">
                <label>{{ __('First name') }} <span class="text-danger">*</span></label>
                <input type="text" name="first_name" value="{{ $lead->first_name }}" required="required" class="form-control form-control-lg">
            </div>
            <div class="col">
                <label>{{ __('Last name') }}</label>
                <input type="text" name="last_name" value="{{ $lead->last_name }}" class="form-control form-control-lg">
            </div>
        </div>
        <div class="row">
            <div class="col">
                <label>{{ __('Phone') }}</label>
                <input type="tel" name="phone" id="phone" value="{{ $lead->phone }}" maxlength="15" class="form-control form-control-lg">
            </div>
            <div class="col">
                <label>E-mail</label>
                <input type="email" name="email" id="email" value="{{ $lead->email }}" maxlength="254" class="form-control form-control-lg">
            </div>
        </div>
        <div class="row">
            <div class="col">
                <label>{{ __('Company') }}</label>
                <input type="text" name="company" id="company" value="{{ $lead->company }}" class="form-control form-control-lg">
            </div>
            <div class="col">
                <label>{{ __('Country') }} <span class="text-danger">*</span></label>
                <select name="country_id" id="country_id" required class="form-control form-control-lg">
                    <option value=""></option>
                    @foreach ($countries as $country)
                    <option value="{{ $country->code_2 }}" @if($lead->country_id == $country->code_2) selected="selected" @endif>{{ $country->name }} {{ $country->flag }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <label>Website</label>
                <input type="url" name="website" id="website" value="{{ $lead->website }}" class="form-control form-control-lg">
            </div>
        </div>
        <div class="row">
            <div class="col mt-2">
                <a href="/customer" class="btn btn-lg btn-outline-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-lg btn-primary">{{ __('Save') }}</button>
            </div>
        </div>
        <input type="hidden" name="id" value="{{ (!empty($lead)) ? $lead->id : '' }}">
    </form>
@endsection
