<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
        }
    </style>
</head>

<body style="background-color: #edf2f6; padding-left: 30px; padding-right: 30px; margin: 0">
    <h1 style="text-align: center">{{ env('APP_NAME') }}</h1>
    <div
        style="width: 70%; background-color: white; margin-left: 15%; margin-right: 15%; padding-bottom: 30px; padding-top: 1px; padding-left: 35px">
        <p><strong>{{ __('Hi') }}, {{ $customer->name }}</strong></p>
        <p style="color: #6799cc">{{ $body }}</p>
        <div style="text-align: center; margin-top: 30px; margin-bottom: 30px">
            <a href="{{ url('ticket/update', $ticket_number) }}" style="padding: 8px 12px; border: 1px solid #2e3748;border-radius: 5px;font-family: Helvetica, Arial, sans-serif;font-size: 14px; color: #ffffff;text-decoration: none;font-weight:bold;display: inline-block; background-color: #2e3748">{{ __('See Ticket') }}</a>
        </div>
        <p style="color: #6799cc; margin: 0">{{ __('Greetings') }},</p>
        <p style="color: #6799cc; margin: 0">{{ env('APP_NAME') }}</p>
    </div>
</body>

</html>

