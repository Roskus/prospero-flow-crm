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
                <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $user->first_name) }}" required autofocus
                       maxlength="80" class="form-control form-control-lg">
            </div>
            <div class="col">
                <label for="last_name" class="control-label">{{ __('Last name') }}</label>
                <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $user->last_name) }}" required
                       maxlength="80" class="form-control form-control-lg">
            </div>
        </div>

        <div class="row form-group mb-3">
            <div class="col">
                <label for="email" class="col-md-4 control-label">E-Mail</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required maxlength="255" autocomplete="off"
                       class="form-control form-control-lg">
            </div>
            <div class="col">
                <label for="phone" class="col-md-4 control-label">{{ __('Phone') }}</label>
                <input type="tel" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" maxlength="15"
                       class="form-control form-control-lg">
            </div>
        </div>

        <div class="row form-group mb-3">
            <div class="col">
                <label for="lang" class="col-md-4 control-label">{{ __('Language') }}</label>
                <select name="lang" id="lang" required="required" class="form-select form-control-lg">
                  <option value=""></option>
                  @foreach ($languages as $code => $name)
                  <option value="{{ $code }}" @selected(old('lang', $user->lang) == $code)>{{ $name }}</option>
                  @endforeach
                </select>
            </div>
            @if($roles->isNotEmpty() && Auth::user()->canChangeRolesOf($user))
            <div class="col">
                <label for="roles">{{ __('Roles') }}</label>
                <select name="roles[]" id="roles" multiple class="form-select form-control-lg">
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" @selected(in_array($role->name, old('roles', $user->getRoleNames()->all())))>{{ __($role->name) }}</option>
                    @endforeach
                </select>
            </div>
            @endif
        </div>

        <div class="row form-group mb-3">
            <div class="col {{ $errors->has('password') ? ' has-error' : '' }}">
                <label for="password" class="control-label">{{ __('Password') }}</label>
                <input id="password" type="password" name="password" autocomplete="new-password"
                       class="form-control form-control-lg">
                @if ($errors->has('password'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
            </div>
            <div class="col">
                <label for="password-confirm" class="control-label">{{ __('Confirm password') }}</label>
                <input type="password" name="password_confirmation" id="password-confirm" autocomplete="new-password"
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
                        <option value="{{ $company->id }}" @selected(old('company_id', $user->exists ? $user->defaultCompanyId() : null) == $company->id)>{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col">
                <label for="extra_companies">{{ __('Access to other companies') }}</label>
                <input type="hidden" name="extra_companies[]" value="">
                @php($extraCompanyIds = old('extra_companies', $user->exists ? $user->extraCompanies()->pluck('company.id')->all() : []))
                @php($extraCompanyIds = is_array($extraCompanyIds) ? array_map('intval', $extraCompanyIds) : [])
                <select name="extra_companies[]" id="extra_companies" multiple class="form-select form-control-lg">
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" @selected(in_array($company->id, $extraCompanyIds, true))>{{ $company->name }}</option>
                    @endforeach
                </select>
                <small class="form-text text-muted">{{ __('The user can switch to these companies from the top bar.') }}</small>
            </div>
            @endif
            <div class="col">
                <label for="default_branch_id" class="control-label">{{ __('Default branch') }}</label>
                <select name="default_branch_id" id="default_branch_id" class="form-select form-control-lg @error('default_branch_id') is-invalid @enderror">
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" data-company-id="{{ $branch->company_id }}" @selected(old('default_branch_id', $user->default_branch_id) == $branch->id)>{{ $branch->code }} - {{ $branch->name }}</option>
                    @endforeach
                </select>
                @error('default_branch_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col">
                <label for="timezone" class="control-label">{{ __('Time zone') }}</label>
                <input name="timezone" id="timezone" list="timezoneOptions" value="{{ old('timezone', $user->timezone) }}" placeholder="{{ __('Type to search') }}..." autocomplete="off"  class="form-control form-control-lg">

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
            <input class="form-check-input" type="checkbox" role="switch" id="is_employee" name="is_employee" value="1" @checked(old('is_employee', $user->is_employee))>
            <label class="form-check-label" for="is_employee">{{ __('Employee') }}</label>
        </div>
    </div>
    <div class="card-body" id="employeeFields" style="{{ old('is_employee', $user->is_employee) ? '' : 'display:none' }}">
        <div class="row form-group mb-3">
            <div class="col-md-3">
                <label for="employee_number" class="control-label">{{ __('Employee #') }}</label>
                <input type="text" name="employee_number" id="employee_number" value="{{ old('employee_number', $user->employee_number) }}" maxlength="50" class="form-control form-control-lg">
            </div>
            <div class="col-md-3">
                <label for="hire_date" class="control-label">{{ __('Hire date') }}</label>
                <input type="date" name="hire_date" id="hire_date" value="{{ old('hire_date', $user->hire_date?->format('Y-m-d')) }}" class="form-control form-control-lg">
            </div>
            <div class="col-md-3">
                <label for="manager_id" class="control-label">{{ __('Manager') }}</label>
                <select name="manager_id" id="manager_id" class="form-select form-control-lg">
                    <option value=""></option>
                    @foreach(\App\Models\User::where('company_id', Auth::user()->company_id)->where('is_employee', true)->get() as $u)
                    <option value="{{ $u->id }}" @selected(old('manager_id', $user->manager_id) == $u->id)>{{ $u->first_name }} {{ $u->last_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="vacation_days_override" class="control-label">{{ __('Vacation days') }}</label>
                <input type="number" name="vacation_days_override" id="vacation_days_override" value="{{ old('vacation_days_override', $user->vacation_days_override) }}" class="form-control form-control-lg" placeholder="{{ $user->company ? __('Default') . ': ' . $user->company->vacation_days_per_year : '' }}">
            </div>
        </div>
        <div class="row form-group mb-3">
                <div class="col-md-3">
                    <label for="weekly_hours_override" class="control-label">{{ __('Weekly hours') }}</label>
                    <input type="number" step="0.5" name="weekly_hours_override" id="weekly_hours_override" value="{{ old('weekly_hours_override', $user->weekly_hours_override) }}" class="form-control form-control-lg" placeholder="{{ $user->company ? __('Default') . ': ' . $user->company->weekly_hours_full_time : '' }}">
                </div>
                @if(Auth::user()->canSeeBankDetails())
                <div class="col-md-3">
                    <label for="iban" class="control-label">{{ __('IBAN') }}</label>
                    <input type="text" name="iban" id="iban" value="{{ old('iban', $user->iban) }}" maxlength="34" class="form-control form-control-lg" placeholder="ES0000000000000000000000">
                </div>
                @endif
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

const companySelect = document.getElementById('company_id');
const branchSelect = document.getElementById('default_branch_id');
function filterBranchesByCompany() {
    for (const option of branchSelect.options) {
        const otherCompany = option.dataset.companyId !== companySelect.value;
        option.hidden = otherCompany;
        option.disabled = otherCompany;
    }
    if (branchSelect.selectedOptions[0]?.disabled !== false) {
        const firstBranch = [...branchSelect.options].find(option => !option.disabled);
        branchSelect.value = firstBranch ? firstBranch.value : '';
    }
}
if (companySelect) {
    companySelect.addEventListener('change', filterBranchesByCompany);
    filterBranchesByCompany();
}
</script>
@endpush
