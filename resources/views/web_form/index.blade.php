@extends('layouts.app')

@section('content')
    <header>
        <h1>{{ __('Web forms') }}</h1>
    </header>

    <div class="mb-2">
        <a href="{{ url('/web-form/create') }}" class="btn btn-primary">{{ __('New') }}</a>
    </div>


@endsection
