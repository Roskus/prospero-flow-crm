@extends('layouts.app')

@section('content')
    <?php
    // @TODO move this to the controller
    $today = \Carbon\Carbon::now();
    $today_day_number = $today->day;
    $day_of_week = \Carbon\Carbon::now()->dayOfWeek;
    $week = [];
    switch ($day_of_week)
    {
        case 0:
            $week[0] = $today_day_number;
            $week[1] = $today_day_number + 1;
            $week[2] = $today_day_number + 2;
            $week[3] = $today_day_number + 3;
            $week[4] = $today_day_number + 4;
            $week[5] = $today_day_number + 5;
            $week[6] = $today_day_number + 6;
        break;
        case 1:
            $week[0] = $today_day_number - 1;
            $week[1] = $today_day_number;
            $week[2] = $today_day_number + 1;
            $week[3] = $today_day_number + 2;
            $week[4] = $today_day_number + 3;
            $week[5] = $today_day_number + 4;
            $week[6] = $today_day_number + 5;
        break;
        case 2:
            $week[0] = $today_day_number - 2;
            $week[1] = $today_day_number - 1;
            $week[2] = $today_day_number;
            $week[3] = $today_day_number + 1;
            $week[4] = $today_day_number + 2;
            $week[5] = $today_day_number + 3;
            $week[6] = $today_day_number + 4;
            break;
        case 3:
            $week[0] = $today_day_number - 3;
            $week[1] = $today_day_number - 2;
            $week[2] = $today_day_number - 1;
            $week[3] = $today_day_number;
            $week[4] = $today_day_number + 1;
            $week[5] = $today_day_number + 2;
            $week[6] = $today_day_number + 3;
            break;
        case 4:
            $week[0] = $today_day_number - 4;
            $week[1] = $today_day_number - 3;
            $week[2] = $today_day_number - 2;
            $week[3] = $today_day_number - 1;
            $week[4] = $today_day_number;
            $week[5] = $today_day_number + 1;
            $week[6] = $today_day_number + 2;
            break;
        case 5:
            $week[0] = $today_day_number - 5;
            $week[1] = $today_day_number - 4;
            $week[2] = $today_day_number - 3;
            $week[3] = $today_day_number - 2;
            $week[4] = $today_day_number - 1;
            $week[5] = $today_day_number;
            $week[6] = $today_day_number + 1;
            break;
        case 6:
            $week[0] = $today_day_number - 6;
            $week[1] = $today_day_number - 5;
            $week[2] = $today_day_number - 4;
            $week[3] = $today_day_number - 3;
            $week[4] = $today_day_number - 2;
            $week[5] = $today_day_number - 1;
            $week[6] = $today_day_number;
            break;
    }
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
