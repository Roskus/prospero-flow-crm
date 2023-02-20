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
        
        @yield('content')

        <p style="color: #6799cc; margin: 0">{{ __('Greetings') }},</p>
        <p style="color: #6799cc; margin: 0">{{ env('APP_NAME') }}</p>
    </div>
</body>

</html>
