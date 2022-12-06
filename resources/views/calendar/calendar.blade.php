@extends('layouts.app')

@section('content')
    <header>
        <h1>{{ __('Calendar') }}</h1>
    </header>

    <div class="row m-2">
        <div class="card">
            <div class="card-body d-flex">
                <a href="{{ route('calendar.index') }}" class="btn btn-outline-dark me-2"
                    title="{{ __('Today') }}">{{ __('Today') }}</a>
                <a href="{{ route('calendar.index',$date->copy()->subMonth()->toDateString()) }}"
                    class="btn btn-outline-dark me-2" title="{{ __('Previous month') }}"><i
                        class="las la-chevron-left"></i></a>
                <a href="{{ route('calendar.index',$date->copy()->addMonth()->toDateString()) }}"
                    class="btn btn-outline-dark me-2" title="{{ __('Next month') }}"><i
                        class="las la-chevron-right"></i></a>
                <span class="fs-5">{{ __('calendar.'.$date->format('F')) }} {{ $date->format('Y') }}</span>
            </div>
        </div>
    </div>

    <div class="row m-2">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col text-center border">{{ __('calendar.Monday') }}</div>
                    <div class="col text-center border">{{ __('calendar.Tuesday') }}</div>
                    <div class="col text-center border">{{ __('calendar.Wednesday') }}</div>
                    <div class="col text-center border">{{ __('calendar.Thursday') }}</div>
                    <div class="col text-center border">{{ __('calendar.Friday') }}</div>
                    <div class="col text-center border">{{ __('calendar.Saturday') }}</div>
                    <div class="col text-center border">{{ __('calendar.Sunday') }}</div>
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

    @include('calendar.modal.event_create')

    @push('scripts')
        <script> var route_calendar_controller = "{{ route('calendar.index') }}"; </script>
        <script src="{{ asset('/asset/js/Calendar.js') }}"></script>
    @endpush
@endsection
