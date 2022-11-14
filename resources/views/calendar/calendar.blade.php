@extends('layouts.app')

@section('content')
    <header>
        <h1>{{ __('Calendar') }}</h1>
    </header>

    <div class="row">
        <div class="col text-center">
            <a onclick="">{{ __('Today') }}</a>
        </div>
        <div class="col text-center">
            {{ __('calendar.'.date('F')) }} {{ date('Y') }}
        </div>
        <div class="col text-center">
            <div>{{ __('Day') }}</div>
            <div>
                <a onclick="" class="active">{{ __('Week') }}</a>
            </div>
            <div>{{ __('Month') }}</div>
        </div>
    </div>

    <!--week-->

    <table class="table table-bordered table-striped table-hovered">
    <thead>
        <tr>
            <td>&nbsp;</td>
            <td class="text-center @if($day_of_week == 0) fw-bold @endif">{{ __('calendar.Sunday') }} {{ $week[0] }}</td>
            <td class="text-center @if($day_of_week == 1) fw-bold @endif">{{ __('calendar.Monday') }} {{ $week[1] }}</td>
            <td class="text-center @if($day_of_week == 2) fw-bold @endif">{{ __('calendar.Tuesday') }} {{ $week[2] }}</td>
            <td class="text-center @if($day_of_week == 3) fw-bold @endif">{{ __('calendar.Wednesday') }} {{ $week[3] }}</td>
            <td class="text-center @if($day_of_week == 4) fw-bold @endif">{{ __('calendar.Thursday') }} {{ $week[4] }}</td>
            <td class="text-center @if($day_of_week == 5) fw-bold @endif">{{ __('calendar.Friday') }} {{ $week[5] }}</td>
            <td class="text-center @if($day_of_week == 6) fw-bold @endif">{{ __('calendar.Saturday') }} {{ $week[6] }}</td>
        </tr>
    </thead>
    <tbody>
        @for($h=6; $h < 23; $h++)
        <tr>
            <td class="text-center">{{ $h }}:00</td>
            <td class="text-center">
                <a onclick="Calendar.scheduleEvent('{{ date('Y-m-').$week[0].' '.$h }}:00')" class="btn btn-primary rounded">+</a>
            </td>
            <td class="text-center">
                <a onclick="Calendar.scheduleEvent('{{ date('Y-m-').$week[1] }}')" class="btn btn-primary rounded">+</a>
            </td>
            <td class="text-center">
                <a onclick="Calendar.scheduleEvent('{{ date('Y-m-').$week[2] }}')" class="btn btn-primary rounded">+</a>
            </td>
            <td class="text-center">
                <a onclick="Calendar.scheduleEvent('{{ date('Y-m-').$week[3] }}')" class="btn btn-primary rounded">+</a>
            </td>
            <td class="text-center">
                <a onclick="Calendar.scheduleEvent('{{ date('Y-m-').$week[4] }}')" class="btn btn-primary rounded">+</a>
            </td>
            <td class="text-center">
                <a onclick="Calendar.scheduleEvent('{{ date('Y-m-').$week[5] }}')" class="btn btn-primary rounded">+</a>
            </td>
            <td class="text-center">
                <a onclick="Calendar.scheduleEvent('{{ date('Y-m-').$week[6] }}')" class="btn btn-primary rounded">+</a>
            </td>
        </tr>
        @endfor
    </tbody>
    </table>
    <div id="sheduleEventModal" class="modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Schedule event') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="calendar">
                        <div class="row">
                            <div class="col">
                                <label>{{ __('Title') }} <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" required="required" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <label>{{ __('Guests') }} <span class="text-danger">*</span></label>
                                <input type="text" name="guests" id="guests" required="required" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <label>{{ __('Date start') }} <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="date_start" id="date_start" required="required" class="form-control" min="{{ date('Y-m-d H:i') }}">
                            </div><!--./col-->
                            <div class="col">
                                <label>{{ __('Date end') }}</label>
                                <input type="datetime-local" name="date_end" id="date_end" class="form-control" min="{{ date('Y-m-d H:i') }}">
                            </div><!--./col-->
                        </div>
                        <div class="row">
                            <div class="col">
                                <label>{{ __('Web meeting') }}</label>
                                <input type="url" name="meeting" id="meeting" placeholder="https://meet.jit.si/{{ Auth::user->company->name }}" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <label>{{ __('Description') }}</label>
                                <textarea name="description" id="description" class="form-control"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" onclick="Calendar.save()" class="btn btn-primary">{{ __('Save') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!--week-->
    @push('scripts')
        <script src="{{ asset('/asset/js/Calendar.js') }}"></script>
    @endpush
@endsection
