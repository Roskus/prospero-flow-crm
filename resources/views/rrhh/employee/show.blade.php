@extends('layouts.app')

@section('content')
@include('layouts.partials._header', ['title' => $employee->first_name.' '.$employee->last_name])

<div class="row">
    <div class="col-md-4">
        <div class="card mb-2">
            <div class="card-body">
                <h5>{{ __('Employee Info') }}</h5>
                <dl class="row">
                    <dt class="col-sm-5">{{ __('Employee #') }}</dt>
                    <dd class="col-sm-7">{{ $employee->employee_number ?? '—' }}</dd>
                    <dt class="col-sm-5">{{ __('Name') }}</dt>
                    <dd class="col-sm-7">{{ $employee->first_name }} {{ $employee->last_name }}</dd>
                    <dt class="col-sm-5">{{ __('Hire date') }}</dt>
                    <dd class="col-sm-7">{{ $employee->hire_date?->format('Y-m-d') ?? '—' }}</dd>
                    <dt class="col-sm-5">{{ __('Email') }}</dt>
                    <dd class="col-sm-7">{{ $employee->email }}</dd>
                    <dt class="col-sm-5">{{ __('Phone') }}</dt>
                    <dd class="col-sm-7">{{ $employee->phone ?? '—' }}</dd>
                    <dt class="col-sm-5">{{ __('Manager') }}</dt>
                    <dd class="col-sm-7">{{ $employee->manager?->first_name }} {{ $employee->manager?->last_name ?? '—' }}</dd>
                </dl>
                <a href="{{ url("/user/update/{$employee->id}") }}" class="btn btn-warning text-white">{{ __('Edit') }}</a>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card mb-2">
            <div class="card-body">
                <h5>{{ __('HR Settings') }}</h5>
                <dl class="row">
                    <dt class="col-sm-4">{{ __('Vacation days') }}</dt>
                    <dd class="col-sm-8">{{ $employee->vacation_days_override ?? $employee->company->vacation_days_per_year }} {{ __('per year') }}</dd>
                    <dt class="col-sm-4">{{ __('Weekly hours') }}</dt>
                    <dd class="col-sm-8">{{ $employee->weekly_hours_override ?? $employee->company->weekly_hours_full_time }} h</dd>
                    <dt class="col-sm-4">{{ __('IBAN') }}</dt>
                    <dd class="col-sm-8">{{ $employee->iban ?? '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
