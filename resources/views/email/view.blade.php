@extends('layouts.app')
@section('content')
    <div class="container">
        <header>
            <h1>{{ $email->subject }}</h1>
        </header>
        <div>
            {!! $email->body !!}
        </div>
    </div>
@endsection
