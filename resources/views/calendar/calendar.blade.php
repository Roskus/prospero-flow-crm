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
            {{ date('Y') }}
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
    <?php
        echo \Carbon\Carbon::now()->dayOfWeek;
    ?>
    <table class="table table-bordered table-striped table-hovered">
    <thead>
        <tr>
            <td>&nbsp;</td>
            <td class="text-center">{{ __('Sunday') }}</td>
            <td class="text-center">{{ __('Monday') }}</td>
            <td class="text-center">{{ __('Tuesday') }}</td>
            <td class="text-center">{{ __('Wednesday') }}</td>
            <td class="text-center">{{ __('Thursday') }}</td>
            <td class="text-center">{{ __('Friday') }}</td>
            <td class="text-center">{{ __('Saturday') }}</td>
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
