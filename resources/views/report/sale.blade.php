@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', ['title' => __('Sales report')])
    @include('layouts.partials._errors')

    <form method="get" action="{{ url('/report/sale') }}" class="row g-2 align-items-end mb-3 d-print-none" id="sales-report-filters">
        <div class="col-6 col-md-3">
            <label for="from" class="form-label">{{ __('From') }}</label>
            <input type="date" name="from" id="from" value="{{ $from->format('Y-m-d') }}" class="form-control">
        </div>
        <div class="col-6 col-md-3">
            <label for="to" class="form-label">{{ __('To') }}</label>
            <input type="date" name="to" id="to" value="{{ $to->format('Y-m-d') }}" class="form-control">
        </div>
        @if($can_see_all_sellers)
            <div class="col-12 col-md-4">
                <label for="seller_id" class="form-label">{{ __('Seller') }}</label>
                <select name="seller_id" id="seller_id" class="form-select">
                    <option value="">{{ __('All') }}</option>
                    @foreach($sellers as $seller)
                        <option value="{{ $seller->id }}" @selected($seller_id === $seller->id)>
                            {{ $seller->first_name }} {{ $seller->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
        <div class="col-12 col-md-2 d-grid">
            <button type="submit" class="btn btn-primary">
                <i class="las la-filter"></i> {{ __('Filter') }}
            </button>
        </div>
    </form>

    <p class="text-muted small">
        {{ __('Compared with the previous period') }}:
        {{ $report['previous_from']->format('d/m/Y') }} - {{ $report['previous_to']->format('d/m/Y') }}
        <span class="ms-2"><span class="badge bg-primary">&nbsp;</span> {{ __('Current period') }}</span>
        <span class="ms-2"><span class="badge bg-secondary">&nbsp;</span> {{ __('Previous period') }}</span>
    </p>

    @php
        $kpis = [
            'total' => ['label' => __('Total sales'), 'currency' => true, 'higher_is_better' => true],
            'orders' => ['label' => __('Orders'), 'currency' => false, 'higher_is_better' => true],
            'average_ticket' => ['label' => __('Average ticket'), 'currency' => true, 'higher_is_better' => true],
            'pending_orders' => ['label' => __('Pending orders'), 'currency' => false, 'higher_is_better' => false],
        ];
    @endphp

    <div class="row" id="sales-report-kpis">
        @foreach($kpis as $key => $kpi)
            @php
                $current = $report['current'][$key];
                $previous = $report['previous'][$key];
                $change = $report['change'][$key];
                $max = max($current, $previous);
                $currentWidth = $max > 0 ? round($current / $max * 100) : 0;
                $previousWidth = $max > 0 ? round($previous / $max * 100) : 0;
                $isImprovement = $change !== null && ($kpi['higher_is_better'] ? $change >= 0 : $change <= 0);
                $format = fn ($value) => $kpi['currency'] ? \App\Helpers\CurrencyHelper::format((float) $value) : $value;
            @endphp
            <div class="col-sm-6 col-xl-3 mb-3">
                <div class="card h-100" data-kpi="{{ $key }}">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">{{ $kpi['label'] }}</h6>
                        <div class="fs-3 fw-semibold">{{ $format($current) }}</div>
                        <small class="text-muted d-block mb-2">
                            @if($change !== null)
                                <span class="{{ $isImprovement ? 'text-success' : 'text-danger' }}">
                                    <i class="las {{ $change >= 0 ? 'la-arrow-up' : 'la-arrow-down' }}"></i>
                                    {{ $change > 0 ? '+' : '' }}{{ $change }}%
                                </span>
                            @endif
                            {{ __('Previous period') }}: {{ $format($previous) }}
                        </small>

                        <div class="progress mb-1" role="progressbar" aria-label="{{ __('Current period') }}"
                             aria-valuenow="{{ $currentWidth }}" aria-valuemin="0" aria-valuemax="100" style="height: 8px">
                            <div class="progress-bar" style="width: {{ $currentWidth }}%"></div>
                        </div>
                        <div class="progress" role="progressbar" aria-label="{{ __('Previous period') }}"
                             aria-valuenow="{{ $previousWidth }}" aria-valuemin="0" aria-valuemax="100" style="height: 8px">
                            <div class="progress-bar bg-secondary" style="width: {{ $previousWidth }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <p class="text-muted small">
        <i class="las la-info-circle"></i>
        {{ __('Sales include confirmed and completed orders.') }}
    </p>
@endsection
