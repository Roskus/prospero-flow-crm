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
                <span class="fs-5">{{ $date->format('F Y') }}</span>
            </div>
        </div>
    </div>

    <div class="row m-2">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col text-center border">{{ __('Monday') }}</div>
                    <div class="col text-center border">{{ __('Tuesday') }}</div>
                    <div class="col text-center border">{{ __('Wednesday') }}</div>
                    <div class="col text-center border">{{ __('Thursday') }}</div>
                    <div class="col text-center border">{{ __('Friday') }}</div>
                    <div class="col text-center border">{{ __('Saturday') }}</div>
                    <div class="col text-center border">{{ __('Sunday') }}</div>
                </div>
                <div class="row" style="height: 14vh;">

                    @while ($startOfCalendar <= $endOfCalendar)
                        <div class="col text-center border d-flex flex-column" onclick="Calendar.scheduleEvent('{{ $startOfCalendar->toDateString() }}')">
                            <span class="mb-1">{{ $startOfCalendar->format('j') }}</span>
                            @foreach ( $events->where('start_date', $startOfCalendar) as $event)
                                <span class="badge text-bg-secondary mb-1">{{ $event->title }}</span>
                            @endforeach                            
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

    <div id="sheduleEventModal" class="modal" tabindex="-1">
        <form action="{{ route('calendar.save') }}" method="POST">
            @csrf
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        {{ __('Add event') }}
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col">
                                <input type="text" class="form-control" name="title" id="title" required placeholder="{{ __('Add title') }}">
                            </div>
                        </div>    
                        <div>
                            <div class="row">
                                <div class="col">
                                    <label for="date" class="form-label">{{ __('Date') }}</label>
                                    <input type="date" name="date" id="date" required class="form-control mb-3">
                                </div>
                                <div class="col">
                                    <label for="date" class="form-label">{{ __('Date') }}</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control mb-3">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <label for="start_time" class="form-label">{{ __('Start time') }}</label>
                                    <input type="time" class="form-control" name="start_time" id="start_time">
                                </div>
                                <div class="col">    
                                    <label for="end_time" class="form-label">{{ __('End time') }}</label>
                                    <input type="time" class="form-control" name="end_time" id="end_time">
                                </div>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" value="1" id="is_all_day" name="is_all_day">
                                <label class="form-check-label" for="is_all_day">
                                    {{ __('Is all day?') }}
                                </label>
                            </div>
                            
                            <div class="row">
                                <div class="col">
                                    <label for="description" class="form-label">{{ __('Description') }}</label>
                                    <textarea class="form-control mb-3" name="description" id="description" cols="30" rows="5"></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <label for="guests" class="form-label">{{ __('Guests') }}</label>
                                    <input type="text" class="form-control mb-3" name="guests" id="guests">
                                </div>    
                            </div>
                            <div class="row">
                                <div class="col">
                                    <label for="meeting" class="form-label">{{ __('Meeting') }}</label>
                                    <input type="text" class="form-control mb-3" name="meeting" id="meeting">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <label for="address" class="form-label">{{ __('Address') }}</label>
                                    <input type="text" class="form-control mb-3" name="address" id="address">
                                    <div id="map"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="latitude" id="latitude" value="">
                        <input type="hidden" name="longitude" id="longitude" value="">

                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script src="{{ asset('/asset/js/Calendar.js') }}"></script>
    @endpush
@endsection
