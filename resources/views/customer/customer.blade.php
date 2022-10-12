@extends('layouts.app')

@section('content')
    <header>
        <h1>{{ __('Customer') }}
    </header>
    <form method="POST" action="{{ url('/customer/save') }}" class="form">
        {{ csrf_field() }}
        <div class="row">
            <div class="col">
                <label for="">{{ __('First name') }} <span class="text-danger">*</span></label>
                <input type="text" name="first_name" value="{{ $customer->first_name }}" required="required" class="form-control form-control-lg">
            </div>
            <div class="col">
                <label for="">{{ __('Last name') }}</label>
                <input type="text" name="last_name" value="{{ $customer->last_name }}" class="form-control form-control-lg">
            </div>
        </div><!--./row-->
        <div class="row">
            <div class="col">
                <label for="phone">{{ __('Phone') }}</label>
                <input type="tel" name="phone" id="phone" value="{{ $customer->phone }}" maxlength="15" class="form-control form-control-lg">
            </div>
            <div class="col">
                <label for="mobile">{{ __('Mobile') }}</label>
                <input type="tel" name="mobile" id="mobile" value="{{ $customer->mobile }}" maxlength="15" class="form-control form-control-lg">
            </div>
        </div><!--./row-->
        <div class="row">
            <div class="col">
                <label for="email">E-mail</label>
                <input type="email" name="email" id="email" value="{{ $customer->email }}" maxlength="254" class="form-control form-control-lg">
            </div>
        </div><!--./row-->
        <div class="row">
            <div class="col">
                <label>{{ __('Company') }}</label>
                <input type="text" name="company" id="company" value="{{ $customer->company }}" class="form-control form-control-lg">
            </div>
            <div class="col">
                <label>{{ __('Country') }} <span class="text-danger">*</span></label>
                <select name="country_id" id="country_id" required class="form-select form-control-lg">
                    <option value=""></option>
                    @foreach ($countries as $country)
                    <option value="{{ $country->code_2 }}" @if($customer->country_id == $country->code_2) selected="selected" @endif>{{ $country->name }} {{ $country->flag }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <label for="website">Website</label>
                <input type="url" name="website" id="website" value="{{ $customer->website }}" class="form-control form-control-lg" maxlength="255">
            </div>
        </div>
        <div class="row">
            <div class="col mt-2">
                <a href="{{ url('/customer') }}" class="btn btn-lg btn-outline-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-lg btn-primary">{{ __('Save') }}</button>
            </div>
        </div>
        <input type="hidden" name="id" value="{{ (!empty($customer)) ? $customer->id : '' }}">
    </form>
    @push('scripts')
    <script>
        $('#phone').on('paste', function() {
            let $el = $(this);
            setTimeout(function() {
                $el.val(function(i, val) {
                    return val.replace(/[ -.]/g, '')
                })
            })
        });
    </script>
    @endpush
@endsection
