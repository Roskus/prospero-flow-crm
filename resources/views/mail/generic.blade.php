@extends('mail.templates.default')

@section('content')
    <div style="color: #6799cc">
        {!! $body !!}
        @isset($siganture)
        {!! $siganture !!}
        @endisset
    </div>
@endsection
