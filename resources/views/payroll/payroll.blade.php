@extends('layouts.app')

@section('content')
<header>
    <h1>{{ isset($payroll) ? __('Edit Payroll') : __('New Payroll') }}</h1>
</header>

<form method="POST" action="{{ url('/payroll/save') }}" enctype="multipart/form-data" class="form">
    @csrf
    @if(isset($payroll))
    <input type="hidden" name="id" value="{{ $payroll->id }}">
    @endif

    <div class="card">
        <div class="card-body">
            <div class="row form-group mb-3">
                <div class="col-md-4">
                    <label for="user_id" class="form-label">{{ __('Employee') }} <span class="text-danger">*</span></label>
                    <select name="user_id" id="user_id" required class="form-select">
                        <option value=""></option>
                        @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" @selected(isset($payroll) && $payroll->user_id == $emp->id)>{{ $emp->first_name }} {{ $emp->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="gross_amount" class="form-label">{{ __('Gross') }} <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="gross_amount" id="gross_amount" value="{{ old('gross_amount', $payroll->gross_amount ?? '') }}" required class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="net_amount" class="form-label">{{ __('Net') }} <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="net_amount" id="net_amount" value="{{ old('net_amount', $payroll->net_amount ?? '') }}" required class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="period_year" class="form-label">{{ __('Year') }} <span class="text-danger">*</span></label>
                    <select name="period_year" id="period_year" required class="form-select">
                        @for($y = $current_year - 1; $y <= $current_year + 1; $y++)
                        <option value="{{ $y }}" @selected(isset($payroll) && $payroll->period_year == $y)>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="period_month" class="form-label">{{ __('Month') }} <span class="text-danger">*</span></label>
                    <select name="period_month" id="period_month" required class="form-select">
                        @foreach(range(1, 12) as $m)
                        @php $monthName = \Carbon\Carbon::createFromDate(null, $m, 1)->format('F'); @endphp
                        <option value="{{ $m }}" @selected(isset($payroll) && $payroll->period_month == $m)>{{ __($monthName) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="payment_date" class="form-label">{{ __('Paid on') }}</label>
                    <input type="date" name="payment_date" id="payment_date" value="{{ old('payment_date', isset($payroll) && $payroll->payment_date ? \Illuminate\Support\Carbon::parse($payroll->payment_date)->format('Y-m-d') : '') }}" class="form-control">
                </div>
            </div>
            <div class="row form-group mb-3">
                <div class="col-md-2">
                    <label for="iban" class="form-label">{{ __('IBAN') }}</label>
                    <input type="text" name="iban" id="iban" value="{{ old('iban', $payroll->iban ?? '') }}" maxlength="34" class="form-control" placeholder="{{ isset($payroll) && $payroll->user ? $payroll->user->iban : '' }}">
                </div>
                <div class="col-md-4">
                    <label for="file" class="form-label">{{ __('PDF file') }}</label>
                    <input type="file" name="file" id="file" accept="application/pdf" class="form-control">
                    <small class="text-muted">{{ __('PDF only. Max 10MB.') }}</small>
                    @if(isset($payroll) && $payroll->file)
                    <div class="mt-1"><a href="{{ \Illuminate\Support\Facades\Storage::url($payroll->file) }}" target="_blank">{{ __('View current file') }}</a></div>
                    @endif
                </div>
                <div class="col-md-6">
                    <label for="notes" class="form-label">{{ __('Notes') }}</label>
                    <input type="text" name="notes" id="notes" value="{{ old('notes', $payroll->notes ?? '') }}" class="form-control" placeholder="{{ __('e.g. Extra payment, December') }}">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col">
                    <a href="{{ url('/payroll') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
