@extends('layouts.app')

@section('content')
    <header>
        <h1>E-mail templates</h1>
    </header>

    <div>
        <a href="{{ url('/email-template/create') }}" class="btn btn-primary">{{ __('New') }}</a>
    </div>

    @foreach($templates as $template)
    <div>
        <img src="{{ $template->screenshot }}" alt="">
    </div>
    @endforeach
@endsection
