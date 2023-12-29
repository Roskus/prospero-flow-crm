@extends('mail.templates.default')

@section('content')
    <div style="color: #6799cc; margin-top: 30px; margin-bottom: 30px; font-size: x-large;">
        {!! $data['body'] !!}

        @isset($data['signature'])
            {!! $data['signature'] !!}
        @endisset
    </div>
@endsection
