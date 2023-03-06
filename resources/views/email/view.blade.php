@extends('layouts.app')
@section('content')
    <div class="container">
        <header>
            <h1>{{ $email->subject }}</h1>
        </header>
        <div>
            {!! $email->body !!}
            @isset($email->signature)
            {!! \Illuminate\Support\Facades\Auth::user()->signature_html !!}
            @endisset
        </div>
    </div>
@endsection
