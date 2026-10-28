@extends('layouts.app')

@section('content')
@php
    $user = Auth::user();
    $reportGroups = [
        [
            'title' => __('Sales'),
            'icon' => 'las la-chart-line',
            'items' => [
                ['url' => url('/report/sale'), 'icon' => 'las la-funnel-dollar', 'label' => __('Sales report'), 'description' => __('Sales, orders and average ticket of a period compared with the previous one.'), 'visible' => $user->can('read sales report')],
                ['url' => url('/report/sale/annual'), 'icon' => 'las la-chart-bar', 'label' => __('Annual sales report'), 'description' => __('Month by month sales of a year against the previous year, top products and customers.'), 'visible' => $user->can('read sales report')],
            ],
        ],
        [
            'title' => __('People'),
            'icon' => 'las la-user-clock',
            'items' => [
                ['url' => url('/report/hours'), 'icon' => 'las la-business-time', 'label' => __('Work hours report'), 'description' => __('Worked hours against the schedule of each employee.'), 'visible' => $user->can('read hours report') || $user->can('manage own timesheet')],
            ],
        ],
        [
            'title' => __('Communication'),
            'icon' => 'las la-comments',
            'items' => [
                ['url' => url('/report/email'), 'icon' => 'las la-envelope-open-text', 'label' => __('Email report'), 'description' => __('Email marketing results.'), 'visible' => $user->can('read report')],
            ],
        ],
    ];
@endphp

<header>
   <h1>{{ __('Reports') }}</h1>
</header>

<div class="row g-3">
    @foreach($reportGroups as $reportGroup)
        @php($visibleItems = array_filter($reportGroup['items'], fn (array $item): bool => $item['visible']))
        @continue(empty($visibleItems))
        <div class="col-12 col-lg-6">
            <section class="card h-100">
                <h2 class="card-header bg-transparent h5 fw-semibold mb-0">
                    <i class="{{ $reportGroup['icon'] }}"></i>
                    {{ $reportGroup['title'] }}
                </h2>
                <div class="card-body">
                    <div class="row row-cols-1 row-cols-sm-2 g-2">
                        @foreach($visibleItems as $item)
                            <div class="col">
                                <a href="{{ $item['url'] }}" class="setting-tile d-flex align-items-center gap-3 h-100 px-3 py-2 border rounded text-body text-decoration-none">
                                    <i class="{{ $item['icon'] }} fs-2 text-primary"></i>
                                    <span>
                                        <span class="d-block">{{ $item['label'] }}</span>
                                        <small class="text-muted">{{ $item['description'] }}</small>
                                    </span>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        </div>
    @endforeach
</div>
@endsection
