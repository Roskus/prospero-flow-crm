@extends('layouts.app')

@section('content')
    <header>
        <h1>{{ __('Reports') }}</h1>
    </header>

    <div class="row">
        <div class="col text-center">
            <div class="card">
                <div class="card-body">
                    <a href="{{ url('/report/email') }}">
                        <div class="h1">
                            <i class="las la-envelope-open-text"></i>
                        </div>
                        {{ __('Email report') }}
                    </a>
                </div>
            </div>
        </div><!--./col-->

        <div class="col text-center">
            <div class="card">
                <div class="card-body">
                    <a href="{{ url('/report/sale') }}">
                        <div class="h1">
                            <i class="las la-funnel-dollar"></i>
                        </div>
                        {{ __('Sales report') }}
                    </a>
                </div>
            </div>
        </div><!--./col-->
    </div>
@endsection
