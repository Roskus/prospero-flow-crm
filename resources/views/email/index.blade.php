@extends('layouts.app')

@section('content')
    <header>
        <h1>E-mail</h1>
    </header>

    <div>
        <a href="/email/create" class="btn btn-primary">{{ __('New') }}</a>
    </div>
@endsection
