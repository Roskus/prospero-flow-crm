@extends('layouts.app')

@section('content')
@include('layouts.partials._header', ['title' => __('Payrolls')])

<div class="row mb-2">
    <div class="col">
        <a href="{{ url('/payroll/create') }}" class="btn btn-primary">{{ __('New payroll') }}</a>
    </div>
    <div class="col">
        <form method="get" action="{{ url('/payroll') }}" class="form-inline">
            <div class="input-group">
                <select name="user_id" class="form-select">
                    <option value="">{{ __('All employees') }}</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ request('user_id') == $emp->id ? 'selected' : '' }}>{{ $emp->first_name }} {{ $emp->last_name }}</option>
                    @endforeach
                </select>
                <select name="year" class="form-select">
                    @foreach($years as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
                <button class="btn btn-outline-primary" type="submit"><i class="las la-filter"></i></button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover table-sm">
            <thead>
            <tr>
                <th>{{ __('Employee') }}</th>
                <th>{{ __('Period') }}</th>
                <th>{{ __('Gross') }}</th>
                <th>{{ __('Net') }}</th>
                <th>{{ __('IBAN') }}</th>
                <th>{{ __('File') }}</th>
                <th>{{ __('Notes') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @forelse($payrolls as $payroll)
            <tr>
                <td>{{ $payroll->user->first_name }} {{ $payroll->user->last_name }}</td>
                <td>{{ $payroll->period_year }}-{{ str_pad((string) $payroll->period_month, 2, '0', STR_PAD_LEFT) }}</td>
                <td>{{ number_format($payroll->gross_amount, 2) }}</td>
                <td>{{ number_format($payroll->net_amount, 2) }}</td>
                <td><small>{{ $payroll->iban ?? '—' }}</small></td>
                <td>
                    @if($payroll->file)
                    <a href="{{ \Illuminate\Support\Facades\Storage::url($payroll->file) }}" target="_blank" class="btn btn-xs btn-info text-white"><i class="las la-file-pdf"></i></a>
                    @endif
                </td>
                <td>{{ $payroll->notes }}</td>
                <td class="text-nowrap">
                    <a href="{{ url("/payroll/update/{$payroll->id}") }}" class="btn btn-xs btn-warning text-white"><i class="las la-pen"></i></a>
                    <a href="{{ url("/payroll/delete/{$payroll->id}") }}" class="btn btn-xs btn-danger" onclick="return confirm('{{ __('Are you sure?') }}')"><i class="las la-trash-alt"></i></a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center text-muted">{{ __('No payrolls found') }}</td>
            </tr>
            @endforelse
            </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
