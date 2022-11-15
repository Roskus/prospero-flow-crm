@extends('layouts.app')

@section('content')
    <header>
        <h1>{{ __('Reports') }}</h1>
    </header>

    <div class="row">
        <div class="col">
            <a href="/report/sale">{{ __('Sales report') }}</a>
        </div>
    </div>
@endsection
