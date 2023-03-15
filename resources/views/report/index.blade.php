@extends('layouts.app')

@section('content')
    <header>
        <h1>{{ __('Reports') }}</h1>
    </header>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <a href="{{ url('/report/sale') }}">{{ __('Sales report') }}</a>
                </div>
            </div>
        </div>
    </div>
@endsection
