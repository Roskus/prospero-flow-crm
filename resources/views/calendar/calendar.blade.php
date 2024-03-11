@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', ['title' =>  __('Calendar')])

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
                                <div class="badge text-bg-secondary mb-1 text-wrap">
                                    <span role="button" onclick="Calendar.read('{{ $event->id }}')">{{ $event->title }}</span>
                                    <span>
                                        <a href="{{ url("/calendar/$event->id/export") }}" class="text-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                                 class="bi bi-file-earmark-arrow-down-fill" viewBox="0 0 16 16">
                                                <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0M9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1m-1 4v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 0 1 .708-.708L7.5 11.293V7.5a.5.5 0 0 1 1 0"/>
                                            </svg>
                                        </a>
                                    </span>
                                </div>

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
        <script> const route_calendar_controller = "{{ url(path:'calendar', secure: app()->environment('production')) }}"; </script>
        <script src="{{ url('/asset/js/Calendar.js') }}"></script>
    @endpush
@endsection
