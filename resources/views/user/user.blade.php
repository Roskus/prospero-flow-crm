@extends('layouts.app')

@section('content')
<header>
    <h1>{{ __('User') }}</h1>
</header>

<form method="POST" action="{{ url('/user/save') }}">
@csrf

<div class="card">
    <div class="card-body">
        <div class="row form-group mb-3">
            <div class="col">
                <label for="first_name" class="control-label">{{ __('First name') }}</label>
                <input type="text" name="first_name" id="first_name" value="{{ $user->first_name }}" required autofocus
                       maxlength="80" class="form-control form-control-lg">
            </div>
            <div class="col">
                <label for="last_name" class="control-label">{{ __('Last name') }}</label>
                <input type="text" name="last_name" id="last_name" value="{{ $user->last_name }}" required
                       maxlength="80" class="form-control form-control-lg">
            </div>
        </div>

        <div class="row form-group mb-3">
            <div class="col">
                <label for="email" class="col-md-4 control-label">E-Mail</label>
                <input type="email" name="email" id="email" value="{{ $user->email }}" required maxlength="255"
                       class="form-control form-control-lg">
            </div>
            <div class="col">
                <label for="phone" class="col-md-4 control-label">{{ __('Phone') }}</label>
                <input type="tel" name="phone" id="phone" value="{{ $user->phone }}" maxlength="15"
                       class="form-control form-control-lg">
            </div>
        </div>

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
        </div>

        <div class="row form-group mb-3">
            <div class="col {{ $errors->has('password') ? ' has-error' : '' }}">
                <label for="password" class="control-label">{{ __('Password') }}</label>
                <input id="password" type="password" name="password" autocomplete="off"
                       class="form-control form-control-lg">
                @if ($errors->has('password'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
            </div>
            <div class="col">
                <label for="password-confirm" class="control-label">{{ __('Confirm password') }}</label>
                <input type="password" name="password_confirmation" id="password-confirm" autocomplete="off"
                       class="form-control form-control-lg">
            </div>
        </div>
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
                <input name="timezone" id="timezone" list="timezoneOptions" value="{{ $user->timezone }}" placeholder="{{ __('Type to search') }}..." autocomplete="off"  class="form-control form-control-lg">

                <datalist id="timezoneOptions">
                    @foreach ($timezones as $name)
                        <option value="{{ $name }}">
                    @endforeach
                </datalist>
            </div>
        </div>
    </div>
</div>

<div class="card mt-2">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0">{{ __('Employee Information') }}</h5>
        <div class="form-check form-switch mb-0">
            <input type="hidden" name="is_employee" value="0">
            <input class="form-check-input" type="checkbox" role="switch" id="is_employee" name="is_employee" value="1" {{ $user->is_employee ? 'checked' : '' }}>
            <label class="form-check-label" for="is_employee">{{ __('Employee') }}</label>
        </div>
    </div>
    <div class="card-body" id="employeeFields" style="{{ $user->is_employee ? '' : 'display:none' }}">
        <div class="row form-group mb-3">
            <div class="col-md-3">
                <label for="employee_number" class="control-label">{{ __('Employee #') }}</label>
                <input type="text" name="employee_number" id="employee_number" value="{{ $user->employee_number }}" maxlength="50" class="form-control form-control-lg">
            </div>
            <div class="col-md-3">
                <label for="hire_date" class="control-label">{{ __('Hire date') }}</label>
                <input type="date" name="hire_date" id="hire_date" value="{{ $user->hire_date?->format('Y-m-d') }}" class="form-control form-control-lg">
            </div>
            <div class="col-md-3">
                <label for="manager_id" class="control-label">{{ __('Manager') }}</label>
                <select name="manager_id" id="manager_id" class="form-select form-control-lg">
                    <option value=""></option>
                    @foreach(\App\Models\User::where('company_id', Auth::user()->company_id)->where('is_employee', true)->get() as $u)
                    <option value="{{ $u->id }}" @selected($user->manager_id == $u->id)>{{ $u->first_name }} {{ $u->last_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="vacation_days_override" class="control-label">{{ __('Vacation days') }}</label>
                <input type="number" name="vacation_days_override" id="vacation_days_override" value="{{ $user->vacation_days_override }}" class="form-control form-control-lg" placeholder="{{ __('Default') }}: {{ $user->company->vacation_days_per_year }}">
            </div>
        </div>
        <div class="row form-group mb-3">
                <div class="col-md-3">
                    <label for="weekly_hours_override" class="control-label">{{ __('Weekly hours') }}</label>
                    <input type="number" step="0.5" name="weekly_hours_override" id="weekly_hours_override" value="{{ $user->weekly_hours_override }}" class="form-control form-control-lg" placeholder="{{ __('Default') }}: {{ $user->company->weekly_hours_full_time }}">
                </div>
                <div class="col-md-3">
                    <label for="iban" class="control-label">{{ __('IBAN') }}</label>
                    <input type="text" name="iban" id="iban" value="{{ $user->iban }}" maxlength="34" class="form-control form-control-lg" placeholder="ES0000000000000000000000">
                <input type="number" step="0.5" name="weekly_hours_override" id="weekly_hours_override" value="{{ $user->weekly_hours_override }}" class="form-control form-control-lg" placeholder="{{ __('Default') }}: {{ $user->company->weekly_hours_full_time }}">
            </div>
        </div>
    </div>
</div>

<div class="card mt-2">
    <div class="card-body">
        <div class="row">
            <div class="col text-end">
                <a href="{{ url('/user') }}" class="btn btn-secondary btn-lg">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary btn-lg">{{ __('Save') }}</button>
            </div>
        </div>
    </div>
</div>

<input type="hidden" name="id" value="{{ ($user->id) ?? $user->id }}">
</form>
@endsection

@push('scripts')
<script>
document.getElementById('is_employee').addEventListener('change', function() {
    document.getElementById('employeeFields').style.display = this.checked ? '' : 'none';
});
</script>
@endpush
