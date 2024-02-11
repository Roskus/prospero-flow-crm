@extends('layouts.app')

@section('content')
<header>
    <h1>{{ __('User') }}</h1>
</header>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ url('/user/save') }}">
        @csrf
        <div class="row form-group mb-3">
            <div class="col">
                <label for="first_name" class="control-label">{{ __('First name') }}</label>
                <input type="text" name="first_name" id="first_name" value="{{ $user->first_name }}" required autofocus maxlength="80" class="form-control form-control-lg">
            </div>
            <div class="col">
                <label for="last_name" class="control-label">{{ __('Last name') }}</label>
                <input type="text" name="last_name" id="last_name" value="{{ $user->last_name }}" required maxlength="80" class="form-control form-control-lg">
            </div>
        </div><!--./row-->

        <div class="row form-group mb-3">
            <div class="col">
                <label for="email" class="col-md-4 control-label">E-Mail</label>
                <input type="email" name="email" id="email" value="{{ $user->email }}" required maxlength="255" class="form-control form-control-lg">
            </div>
            <div class="col">
                <label for="phone" class="col-md-4 control-label">{{ __('Phone') }}</label>
                <input type="tel" name="phone" id="phone" value="{{ $user->phone }}" maxlength="15" class="form-control form-control-lg">
            </div>
        </div><!--./row-->

        <div class="row form-group mb-3">
            <div class="col">
                <label for="lang" class="col-md-4 control-label">{{ __('Language') }}</label>
                <select name="lang" id="lang" required="required" class="form-select form-control-lg">
                    <option value=""></option>
                    @foreach ($languages as $code => $name)
                        <option value="{{ $code }}" @if($user->lang == $code) selected="selected" @endif>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            @if(\Illuminate\Support\Facades\Auth::user()->hasRole('SuperAdmin') || \Illuminate\Support\Facades\Auth::user()->hasRole('CompanyAdmin'))
            <div class="col">
                <label for="roles">{{ __('Roles') }}</label>
                <select name="roles[]" id="roles" multiple class="form-select form-control-lg">
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" @if($user->hasRole($role->name)) selected="selected" @endif>{{ __($role->name) }}</option>
                    @endforeach
                </select>
            </div>
            @endif
        </div><!--./row-->

        <div class="row form-group mb-3">
            <div class="col {{ $errors->has('password') ? ' has-error' : '' }}">
                <label for="password" class="control-label">{{ __('Password') }}</label>
                <input id="password" type="password" name="password" autocomplete="off"  class="form-control form-control-lg">
                @if ($errors->has('password'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
            </div><!--./col-->
            <div class="col">
                <label for="password-confirm" class="control-label">{{ __('Confirm password') }}</label>
                <input type="password" name="password_confirmation" id="password-confirm" autocomplete="off" class="form-control form-control-lg">
            </div>
        </div><!--./row-->
        <div class="row form-group mb-3">
            @if(\Illuminate\Support\Facades\Auth::user()->hasRole('SuperAdmin'))
            <div class="col">
                <label for="company_id">{{ __('Company') }}</label>
                <select name="company_id" id="company_id" required="required" class="form-select form-control-lg">
                    <option value=""></option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" @if($user->company_id == $company->id) selected="selected" @endif>{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col">
                <label for="timezone" class="control-label">{{ __('Time zone') }}</label>
                <input name="timezone" id="timezone" list="timezoneOptions" value="{{ $user->timezone }}" placeholder="{{ __('Type to search...') }}" autocomplete="off"  class="form-control form-control-lg">

                <datalist id="timezoneOptions">
                    @foreach ($timezones as $name)
                        <option value="{{ $name }}">
                    @endforeach
                </datalist>
            </div>
        </div><!--/row-->
        <div class="row form-group">
            <div class="col text-end">
                <a href="{{ url('/user') }}" class="btn btn-secondary btn-lg">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary btn-lg">{{ __('Save') }}</button>
            </div>
        </div>
        <input type="hidden" name="id" value="{{ ($user->id) ?? $user->id }}">
        </form>
    </div><!--./card-body-->
</div><!--./card-->
@endsection
