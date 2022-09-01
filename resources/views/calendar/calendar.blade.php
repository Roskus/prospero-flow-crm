@extends('layouts.app')

@section('content')
    <?php
    $today = \Carbon\Carbon::now()->day;
    $day_of_week = \Carbon\Carbon::now()->dayOfWeek;
    ?>
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
            <td class="text-center @if($day_of_week == 0) fw-bold @endif">{{ __('calendar.Sunday') }}</td>
            <td class="text-center @if($day_of_week == 1) fw-bold @endif">{{ __('calendar.Monday') }}</td>
            <td class="text-center @if($day_of_week == 2) fw-bold @endif">{{ __('calendar.Tuesday') }}</td>
            <td class="text-center @if($day_of_week == 3) fw-bold @endif">{{ __('calendar.Wednesday') }}</td>
            <td class="text-center @if($day_of_week == 4) fw-bold @endif">{{ __('calendar.Thursday') }}</td>
            <td class="text-center @if($day_of_week == 5) fw-bold @endif">{{ __('calendar.Friday') }}</td>
            <td class="text-center @if($day_of_week == 6) fw-bold @endif">{{ __('calendar.Saturday') }}</td>
        </tr>
    </thead>
    <tbody>
        @for($h=6; $h < 23; $h++)
        <tr>
            <td class="text-center">{{ $h }}:00</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        @endfor
    </tbody>
    </table>
    <!--week-->

@endsection
