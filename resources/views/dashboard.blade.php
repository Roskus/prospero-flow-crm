@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', ['title' =>  __('Dashboard')])

    <div>
        <div class="row">
            <div class="col-md-6 col-xl-6 mb-2">
                @include('lead.partials._list', [
                    'leads' => $leads,
                    'url' => url('/lead'),
                    'title' => __('Leads')
                ])
                <!-- Last Leads -->

                @include('lead.partials._list', [
                    'leads' => $customers,
                    'url' => url('/customer'),
                    'title' => __('Customers in progress')
                ])
                <!-- Customeres in progress -->
            </div>

            <!--widget.calendar-->
            <div class="col-md-6 col-xl-6 mb-2">
                <div class="card">
                    <div class="card-header">
                        <h4 class="d-flex d-flex justify-content-between m-0">
                            <a href="{{ url('/calendar') }}" class="text-decoration-none text-body">{{ __('Calendar') }}</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col text-center border">
                                <span class="d-none d-xs-block">{{ __('calendar.Mon') }}</span>
                                <span class="d-none d-md-block">{{ __('calendar.Monday') }}</span>
                            </div>
                            <div class="col text-center border">
                                <span class="d-none d-xs-block">{{ __('calendar.Tue') }}</span>
                                <span class="d-none d-md-block">{{ __('calendar.Tuesday') }}</span>
                            </div>
                            <div class="col text-center border">
                                <span class="d-none d-xs-block">{{ __('calendar.Wed') }}</span>
                                <span class="d-none d-md-block">{{ __('calendar.Wednesday') }}</span>
                            </div>
                            <div class="col text-center border">
                                <span class="d-none d-xs-block">{{ __('calendar.Thu') }}</span>
                                <span class="d-none d-md-block">{{ __('calendar.Thursday') }}</span>
                            </div>
                            <div class="col text-center border">
                                <span class="d-none d-xs-block">{{ __('calendar.Fri') }}</span>
                                <span class="d-none d-md-block">{{ __('calendar.Friday') }}</span>
                            </div>
                            <div class="col text-center border">
                                <span class="d-none d-xs-block">{{ __('calendar.Sat') }}</span>
                                <span class="d-none d-md-block">{{ __('calendar.Saturday') }}</span>
                            </div>
                            <div class="col text-center border">
                                <span class="d-none d-xs-block">{{ __('calendar.Sun') }}</span>
                                <span class="d-none d-md-block">{{ __('calendar.Sunday') }}</span>
                            </div>
                        </div>
                        <div class="row" style="height: 14vh;">
                            @while ($startOfCalendar <= $endOfCalendar)
                                <div class="col text-center border p-1" onclick="Calendar.scheduleEvent('{{ $startOfCalendar->toDateString() }}')">
                                    <span class="mb-1 @if($startOfCalendar->isToday()) badge rounded-pill text-bg-primary @endif">{{ $startOfCalendar->format('j') }}</span>
                                    <div class="d-flex flex-column">
                                        @foreach ( $events->whereBetween('start_date', [$startOfCalendar->copy()->startOfDay(), $startOfCalendar->copy()->endOfDay()]) as $event)
                                            <span role="button" class="badge text-bg-secondary mb-1 text-wrap" onclick="Calendar.read('{{ $event->id }}')">{{ $event->title }}</span>
                                        @endforeach
                                    </div>
                                </div>

                                @if ($startOfCalendar->isoWeekday() == 7 && $startOfCalendar->copy()->addDay() <= $endOfCalendar)
                        </div>
                        <div class="row" style="height: 14vh;">
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

@endsection
