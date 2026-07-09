<div class="row form-group mb-2">
    <div class="col-md-4">
        <label for="first_name">{{ __('First name') }} <span class="text-danger">*</span></label>
        <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $employee->first_name ?? '') }}" required class="form-control">
    </div>
    <div class="col-md-4">
        <label for="last_name">{{ __('Last name') }} <span class="text-danger">*</span></label>
        <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $employee->last_name ?? '') }}" required class="form-control">
    </div>
    <div class="col-md-2">
        <label for="employee_number">{{ __('Employee') }} #</label>
        <input type="text" name="employee_number" id="employee_number" value="{{ old('employee_number', $employee->employee_number ?? '') }}" class="form-control">
    </div>
    <div class="col-md-2">
        <label for="hire_date">{{ __('Hire date') }}</label>
        <input type="date" name="hire_date" id="hire_date" value="{{ old('hire_date', isset($employee) && $employee->hire_date ? $employee->hire_date->format('Y-m-d') : '') }}" class="form-control">
    </div>
    <div class="col-md-2">
        <label for="iban">{{ __('IBAN') }}</label>
        <input type="text" name="iban" id="iban" value="{{ old('iban', $employee->iban ?? '') }}" maxlength="34" class="form-control">
    </div>
    <div class="col-md-2">
        <label for="is_employee">{{ __('Employee') }}</label>
        <select name="is_employee" id="is_employee" class="form-select">
            <option value="1" {{ old('is_employee', $employee->is_employee ?? true) ? 'selected' : '' }}>{{ __('Yes') }}</option>
            <option value="0" {{ old('is_employee', $employee->is_employee ?? true) ? '' : 'selected' }}>{{ __('No') }}</option>
        </select>
    </div>
</div>
<div class="row form-group mb-2">
    <div class="col-md-4">
        <label for="email">E-Mail <span class="text-danger">*</span></label>
        <input type="email" name="email" id="email" value="{{ old('email', $employee->email ?? '') }}" required class="form-control">
    </div>
    <div class="col-md-4">
        <label for="phone">{{ __('Phone') }}</label>
        <input type="text" name="phone" id="phone" value="{{ old('phone', $employee->phone ?? '') }}" class="form-control">
    </div>
    <div class="col-md-4">
        <label for="manager_id">{{ __('Manager') }}</label>
        <select name="manager_id" id="manager_id" class="form-select">
            <option value=""></option>
            @foreach(\App\Models\User::where('company_id', Auth::user()->company_id)->where('is_employee', true)->get() as $u)
            <option value="{{ $u->id }}" {{ old('manager_id', $employee->manager_id ?? '') == $u->id ? 'selected' : '' }}>{{ $u->first_name }} {{ $u->last_name }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="row form-group mb-2">
    <div class="col-md-4">
        <label for="vacation_days_override">{{ __('Vacation days') }}</label>
        <input type="number" name="vacation_days_override" id="vacation_days_override" value="{{ old('vacation_days_override', $employee->vacation_days_override ?? '') }}" class="form-control" placeholder="{{ __('Company default') }}: {{ Auth::user()->company->vacation_days_per_year }}">
    </div>
    <div class="col-md-4">
        <label for="weekly_hours_override">{{ __('Weekly hours') }}</label>
        <input type="number" step="0.5" name="weekly_hours_override" id="weekly_hours_override" value="{{ old('weekly_hours_override', $employee->weekly_hours_override ?? '') }}" class="form-control" placeholder="{{ __('Company default') }}: {{ Auth::user()->company->weekly_hours_full_time }}">
    </div>
</div>
