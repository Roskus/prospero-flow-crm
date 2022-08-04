@extends('layouts.app')

@section('content')
    <header>
        <h1>{{ __('Calendar') }}</h1>
    </header>

    <div class="row">
        <div class="col">
            {{ __('Today') }}
        </div>
        <div class="col">
            {{ date('Y') }}
        </div>
        <div class="col">
            <div>{{ __('Day') }}</div>
            <div>{{ __('Week') }}</div>
            <div>{{ __('Month') }}</div>
        </div>
    </div>



    <table class="table table-bordered table-striped table-hovered">
    <thead>
        <tr>
            <td>&nbsp;</td>
            <td>{{ __('Sunday') }}</td>
            <td>{{ __('Monday') }}</td>
            <td>{{ __('Tuesday') }}</td>
            <td>{{ __('Wednesday') }}</td>
            <td>{{ __('Thursday') }}</td>
            <td>{{ __('Friday') }}</td>
            <td>{{ __('Saturday') }}</td>
        </tr>
    </thead>
    <tbody>
        @for($h=6; $h < 23; $h++)
        <tr>
            <td>{{ $h }}:00</td>
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
@endsection
