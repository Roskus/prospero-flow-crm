@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', ['title' =>  __('Dashboard')])
    @include('layouts.partials._errors')

    <div>
        @isset($sales)
        <div class="row" id="dashboard-sales">
            <div class="col-sm-6 col-xl-4 mb-2">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">{{ __('Sales this month') }}</h6>
                        <div class="fs-3 fw-semibold">{{ \App\Helpers\CurrencyHelper::format($sales['current_month_total']) }}</div>
                        <small class="text-muted">
                            @if($sales['change_percentage'] !== null)
                                <span class="{{ $sales['change_percentage'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    <i class="las {{ $sales['change_percentage'] >= 0 ? 'la-arrow-up' : 'la-arrow-down' }}"></i>
                                    {{ $sales['change_percentage'] > 0 ? '+' : '' }}{{ $sales['change_percentage'] }}%
                                </span>
                            @endif
                            {{ __('Previous month') }}: {{ \App\Helpers\CurrencyHelper::format($sales['previous_month_total']) }}
                        </small>
                    </div>
                </div>
            </div>
            <div class="col-sm-3 col-xl-4 mb-2">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">{{ __('Orders this month') }}</h6>
                        <div class="fs-3 fw-semibold">{{ $sales['current_month_orders'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-3 col-xl-4 mb-2">
                <a href="{{ url('/order') }}" class="text-decoration-none text-body">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">{{ __('Pending orders') }}</h6>
                            <div class="fs-3 fw-semibold">{{ $sales['pending_orders'] }}</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        @endisset

        <div class="row">
            <div class="col-md-6 col-xl-6 mb-2">
                @include('lead.partials._list', [
                    'leads' => $leads,
                    'url' => url('/lead'),
                    'title' => __('Leads'),
                    'createUrl' => url('/lead/create'),
                    'createPermission' => 'create lead',
                ])
                <!-- Last Leads -->

                @include('lead.partials._list', [
                    'leads' => $customers,
                    'url' => url('/customer'),
                    'title' => __('Customers'),
                ])
                <!-- Customers with the latest interactions -->
            </div>

            <!--widget.calendar-->
            <div class="col-md-6 col-xl-6 mb-2">
                <div class="card">
                    <div class="card-header">
                        <h4 class="d-flex d-flex justify-content-between m-0">
                            <span class="d-flex align-items-center gap-2">
                                <a href="{{ url('/calendar') }}" class="text-decoration-none text-body">{{ __('Calendar') }}</a>
                                @can('create calendar')
                                    <button type="button" class="btn btn-sm btn-primary" id="dashboard-add-event" title="{{ __('Add event') }}"
                                            onclick="QuickEvent.open('{{ now()->toDateString() }}')">
                                        <i class="las la-plus"></i>
                                    </button>
                                @endcan
                            </span>
                            <small class="text-muted fs-6 fw-normal">
                                @if($calendarView === \App\Models\Company::CALENDAR_VIEW_WEEK)
                                    {{ $startOfCalendar->format('d/m') }} - {{ $endOfCalendar->format('d/m/Y') }}
                                @else
                                    {{ ucfirst(now()->translatedFormat('F Y')) }}
                                @endif
                            </small>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col text-center border">
                                <span class="d-block d-md-none">{{ __('calendar.Mon') }}</span>
                                <span class="d-none d-md-block">{{ __('calendar.Monday') }}</span>
                            </div>
                            <div class="col text-center border">
                                <span class="d-block d-md-none">{{ __('calendar.Tue') }}</span>
                                <span class="d-none d-md-block">{{ __('calendar.Tuesday') }}</span>
                            </div>
                            <div class="col text-center border">
                                <span class="d-block d-md-none">{{ __('calendar.Wed') }}</span>
                                <span class="d-none d-md-block">{{ __('calendar.Wednesday') }}</span>
                            </div>
                            <div class="col text-center border">
                                <span class="d-block d-md-none">{{ __('calendar.Thu') }}</span>
                                <span class="d-none d-md-block">{{ __('calendar.Thursday') }}</span>
                            </div>
                            <div class="col text-center border">
                                <span class="d-block d-md-none">{{ __('calendar.Fri') }}</span>
                                <span class="d-none d-md-block">{{ __('calendar.Friday') }}</span>
                            </div>
                            <div class="col text-center border">
                                <span class="d-block d-md-none">{{ __('calendar.Sat') }}</span>
                                <span class="d-none d-md-block">{{ __('calendar.Saturday') }}</span>
                            </div>
                            <div class="col text-center border">
                                <span class="d-block d-md-none">{{ __('calendar.Sun') }}</span>
                                <span class="d-none d-md-block">{{ __('calendar.Sunday') }}</span>
                            </div>
                        </div>
                        <div class="row" style="min-height: {{ $calendarView === \App\Models\Company::CALENDAR_VIEW_WEEK ? '10rem' : '14vh' }};">
                            @while ($startOfCalendar <= $endOfCalendar)
                                <div class="col text-center border p-1"
                                     @can('create calendar') role="button" title="{{ __('Add event') }}" onclick="QuickEvent.open('{{ $startOfCalendar->toDateString() }}')" @endcan>
                                    <span class="mb-1 @if($startOfCalendar->isToday()) badge rounded-pill text-bg-primary @endif">{{ $startOfCalendar->format('j') }}</span>
                                    <div class="d-flex flex-column">
                                        @foreach ( $events->whereBetween('start_date', [$startOfCalendar->copy()->startOfDay(), $startOfCalendar->copy()->endOfDay()]) as $event)
                                            <a href="{{ url('/calendar/'.$startOfCalendar->toDateString()) }}?event={{ $event->id }}" onclick="event.stopPropagation()"
                                               class="badge text-bg-secondary mb-1 text-wrap text-decoration-none">{{ \Illuminate\Support\Carbon::parse($event->start_date)->format('H:i') }} - {{ $event->title }}</a>
                                        @endforeach
                                    </div>
                                </div>

                                @if ($startOfCalendar->isoWeekday() == 7 && $startOfCalendar->copy()->addDay() <= $endOfCalendar)
                        </div>
                        <div class="row" style="min-height: {{ $calendarView === \App\Models\Company::CALENDAR_VIEW_WEEK ? '10rem' : '14vh' }};">
                            @endif

                            @php
                                $startOfCalendar->addDay();
                            @endphp
                            @endwhile
                        </div>
                    </div>
                </div>
            </div>
            <!--widget.calendar-->
        </div>
    </div>

    @can('create calendar')
        @include('calendar.modal._quick_event')

        @push('scripts')
            <script src="{{ url('/asset/js/QuickEvent.js') }}"></script>
        @endpush
    @endcan
@endsection
