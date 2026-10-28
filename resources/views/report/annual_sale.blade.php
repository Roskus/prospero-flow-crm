@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', ['title' => __('Annual sales report')])
    @include('layouts.partials._errors')

    @php
        $money = fn (float $value): string => \App\Helpers\CurrencyHelper::format($value);
        $chartData = [
            'labels' => collect(range(1, 12))->map(fn (int $month): string => ucfirst(\Illuminate\Support\Carbon::create($year, $month, 1)->translatedFormat('M')))->all(),
            'series' => [
                ['label' => (string) $year, 'data' => array_column($report['months'], 'total')],
                ['label' => (string) ($year - 1), 'data' => array_column($report['months'], 'previous_total')],
            ],
            'currency' => \App\Helpers\CurrencyHelper::symbol(),
        ];
    @endphp

    <form method="get" action="{{ url('/report/sale/annual') }}" class="row g-2 align-items-end mb-3 d-print-none" id="annual-sales-filters">
        <div class="col-6 col-md-2">
            <label for="year" class="form-label">{{ __('Year') }}</label>
            <select name="year" id="year" class="form-select">
                @for($option = now()->year; $option >= now()->year - 9; $option--)
                    <option value="{{ $option }}" @selected($option === $year)>{{ $option }}</option>
                @endfor
            </select>
        </div>
        @if($can_see_all_sellers)
            <div class="col-6 col-md-4">
                <label for="seller_id" class="form-label">{{ __('Seller') }}</label>
                <select name="seller_id" id="seller_id" class="form-select">
                    <option value="">{{ __('All') }}</option>
                    @foreach($sellers as $sellerOption)
                        <option value="{{ $sellerOption->id }}" @selected($seller_id === $sellerOption->id)>{{ $sellerOption->first_name }} {{ $sellerOption->last_name }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <div class="col-12 col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-fill">
                <i class="las la-filter"></i> {{ __('Filter') }}
            </button>
            <a href="{{ url('/report/sale/annual/pdf') }}?{{ http_build_query(array_filter(['year' => $year, 'seller_id' => $can_see_all_sellers ? $seller_id : null])) }}"
               class="btn btn-outline-danger" id="annual-sales-pdf" title="{{ __('Export PDF') }}">
                <i class="las la-file-pdf"></i> <span class="d-md-none">{{ __('Export PDF') }}</span>
            </a>
        </div>
    </form>

    <div class="row g-3 mb-3" id="annual-sales-totals">
        <div class="col-sm-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-1">{{ __('Sales :year', ['year' => $year]) }}</h6>
                    <div class="fs-3 fw-semibold">{{ $money($report['total']) }}</div>
                    <small class="text-muted">
                        @if($report['change'] !== null)
                            <span class="{{ $report['change'] >= 0 ? 'text-success' : 'text-danger' }}">
                                <i class="las {{ $report['change'] >= 0 ? 'la-arrow-up' : 'la-arrow-down' }}"></i>
                                {{ $report['change'] > 0 ? '+' : '' }}{{ $report['change'] }}%
                            </span>
                        @endif
                        {{ __(':year until :date', ['year' => $year - 1, 'date' => $report['previous_until']->format('d/m/Y')]) }}: {{ $money($report['previous_total']) }}
                    </small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-1">{{ __('Orders') }}</h6>
                    <div class="fs-3 fw-semibold">{{ array_sum(array_column($report['months'], 'orders')) }}</div>
                    <small class="text-muted">{{ $year - 1 }}: {{ array_sum(array_column($report['months'], 'previous_orders')) }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h2 class="h6">{{ __('Monthly sales') }}</h2>
            <div style="position: relative; height: 320px">
                <canvas id="annual-sales-chart" aria-label="{{ __('Monthly sales of :year and :previous', ['year' => $year, 'previous' => $year - 1]) }}" role="img"></canvas>
            </div>
            <script type="application/json" id="annual-sales-data">@json($chartData)</script>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @include('report.partial._annual_sale_tables', ['report' => $report, 'year' => $year])
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"
            integrity="sha384-9nhczxUqK87bcKHh20fSQcTGD4qq5GhayNYSYWqwBkINBhOfQLg/P5HG5lF1urn4" crossorigin="anonymous"></script>
    <script>
        (function () {
            const canvas = document.getElementById('annual-sales-chart');
            if (!canvas || typeof Chart === 'undefined') {
                return;
            }
            const data = JSON.parse(document.getElementById('annual-sales-data').textContent);
            const styles = getComputedStyle(document.documentElement);
            const colors = [styles.getPropertyValue('--bs-primary').trim() || '#0d6efd', styles.getPropertyValue('--bs-secondary').trim() || '#6c757d'];
            const format = new Intl.NumberFormat(document.documentElement.lang || undefined, {maximumFractionDigits: 2});

            new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: data.series.map((series, index) => ({label: series.label, data: series.data, backgroundColor: colors[index], borderRadius: 3})),
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {position: 'bottom'},
                        tooltip: {callbacks: {label: (context) => `${context.dataset.label}: ${data.currency} ${format.format(context.parsed.y)}`}},
                    },
                    scales: {y: {beginAtZero: true, ticks: {callback: (value) => `${data.currency} ${format.format(value)}`}}},
                },
            });
        })();
    </script>
@endpush
