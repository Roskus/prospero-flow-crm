@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', ['title' => __('Work hours report')])
    @include('layouts.partials._errors')

    @php
        $hours = fn (float $value): string => number_format($value, 2);
        $signed = fn (float $value): string => ($value > 0 ? '+' : '').number_format($value, 2);
        $differenceClass = fn (float $value): string => $value > 0 ? 'text-success fw-semibold' : ($value < 0 ? 'text-danger fw-semibold' : '');
        $query = array_filter([
            'month' => $month,
            'from' => $month ? null : $from->toDateString(),
            'to' => $month ? null : $to->toDateString(),
            'employee_id' => $employee_id,
        ]);
    @endphp

    <form method="get" action="{{ url('/report/hours') }}" class="row g-2 align-items-end mb-3 d-print-none" id="hours-report-filters">
        <div class="col-6 col-md-2">
            <label for="month" class="form-label">{{ __('Month') }}</label>
            <input type="month" name="month" id="month" value="{{ $month }}" class="form-control">
        </div>
        <div class="col-6 col-md-2">
            <label for="from" class="form-label">{{ __('From') }}</label>
            <input type="date" name="from" id="from" value="{{ $month ? '' : $from->toDateString() }}" class="form-control">
        </div>
        <div class="col-6 col-md-2">
            <label for="to" class="form-label">{{ __('To') }}</label>
            <input type="date" name="to" id="to" value="{{ $month ? '' : $to->toDateString() }}" class="form-control">
        </div>
        @if($can_see_every_employee)
            <div class="col-6 col-md-3">
                <label for="employee_id" class="form-label">{{ __('Employee') }}</label>
                <select name="employee_id" id="employee_id" class="form-select">
                    <option value="">{{ __('All') }}</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" @selected($employee_id === $employee->id)>{{ $employee->first_name }} {{ $employee->last_name }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <div class="col-12 col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-fill">
                <i class="las la-filter"></i> {{ __('Filter') }}
            </button>
            <a href="{{ url('/report/hours/csv') }}?{{ http_build_query($query) }}" class="btn btn-outline-success" id="hours-report-csv" title="{{ __('Export CSV') }}">
                <i class="las la-file-csv"></i> <span class="d-md-none">{{ __('Export CSV') }}</span>
            </a>
        </div>
    </form>

    <p class="text-muted small">
        {{ __('Period') }}: {{ $from->format('d/m/Y') }} – {{ $to->format('d/m/Y') }}.
        {{ __('Days over 6 hours without a break count one hour less; holidays and approved time off expect no hours.') }}
    </p>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead>
                    <tr>
                        <th>{{ __('Employee') }}</th>
                        <th class="text-end">{{ __('Worked hours') }}</th>
                        <th class="text-end">{{ __('Expected hours') }}</th>
                        <th class="text-end">{{ __('Difference') }}</th>
                        <th class="text-end">{{ __('Days worked') }}</th>
                        <th class="text-end">{{ __('Open entries') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($rows as $row)
                        <tr>
                            <td>
                                @can('read rrhh')
                                    <a href="{{ url('/rrhh/time-entries') }}?user_id={{ $row['employee']->id }}">{{ $row['employee']->first_name }} {{ $row['employee']->last_name }}</a>
                                @else
                                    {{ $row['employee']->first_name }} {{ $row['employee']->last_name }}
                                @endcan
                            </td>
                            <td class="text-end">{{ $hours($row['worked']) }}</td>
                            <td class="text-end">{{ $hours($row['expected']) }}</td>
                            <td class="text-end {{ $differenceClass($row['difference']) }}">{{ $signed($row['difference']) }}</td>
                            <td class="text-end">{{ $row['days_worked'] }}</td>
                            <td class="text-end">
                                @if($row['open_entries'] > 0)
                                    <span class="badge bg-warning text-dark">{{ $row['open_entries'] }}</span>
                                @else
                                    0
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">{{ __('No employees') }}</td>
                        </tr>
                    @endforelse
                    </tbody>
                    <tfoot>
                    <tr class="fw-semibold">
                        <td>{{ __('Total') }}</td>
                        <td class="text-end">{{ $hours($totals['worked']) }}</td>
                        <td class="text-end">{{ $hours($totals['expected']) }}</td>
                        <td class="text-end {{ $differenceClass($totals['difference']) }}">{{ $signed($totals['difference']) }}</td>
                        <td class="text-end">{{ $totals['days_worked'] }}</td>
                        <td class="text-end">{{ $totals['open_entries'] }}</td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
