@extends('mail.templates.default')

@section('content')
    <p><strong>{{ __('Hi') }}, {{ $ticket->customer->name }}</strong></p>
    <p style="color: #6799cc">{{ $body }}</p>
    <div style="text-align: center; margin-top: 30px; margin-bottom: 30px">
        <a href="{{ url('ticket/update', $ticket) }}"
            style="padding: 8px 12px; border: 1px solid #2e3748;border-radius: 5px;font-family: Helvetica, Arial, sans-serif;font-size: 14px; color: #ffffff;text-decoration: none;font-weight:bold;display: inline-block; background-color: #2e3748">{{ __('See Ticket') }}</a>
    </div>
@endsection
