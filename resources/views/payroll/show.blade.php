@extends('layouts.app')

@section('content')
@include('layouts.partials._header', ['title' => __('Payroll')])

<div class="card">
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">{{ __('Employee') }}</dt>
            <dd class="col-sm-9">{{ $payroll->user->first_name }} {{ $payroll->user->last_name }}</dd>
            <dt class="col-sm-3">{{ __('Period') }}</dt>
            <dd class="col-sm-9">{{ $payroll->period_year }}-{{ str_pad((string) $payroll->period_month, 2, '0', STR_PAD_LEFT) }}</dd>
            <dt class="col-sm-3">{{ __('Paid on') }}</dt>
            <dd class="col-sm-9">{{ $payroll->payment_date?->format('Y-m-d') ?? '—' }}</dd>
            <dt class="col-sm-3">{{ __('Gross') }}</dt>
            <dd class="col-sm-9">{{ number_format($payroll->gross_amount, 2) }}</dd>
            <dt class="col-sm-3">{{ __('Net') }}</dt>
            <dd class="col-sm-9">{{ number_format($payroll->net_amount, 2) }}</dd>
            <dt class="col-sm-3">{{ __('Notes') }}</dt>
            <dd class="col-sm-9">{{ $payroll->notes ?? '—' }}</dd>
            @if($payroll->file)
            <dt class="col-sm-3">{{ __('File') }}</dt>
            <dd class="col-sm-9"><a href="{{ \Illuminate\Support\Facades\Storage::url($payroll->file) }}" target="_blank"><i class="las la-file-pdf"></i> {{ __('Download') }}</a></dd>
            @endif
        </dl>
        <a href="{{ url('/payroll') }}" class="btn btn-secondary">{{ __('Back') }}</a>
    </div>
</div>
@endsection
