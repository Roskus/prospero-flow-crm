<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('Preview') }}</title>
</head>
<body style="margin: 5vw; width: 90vw; word-break: break-all;">
    <table style="margin-bottom: 30px">
        <tbody>
            <tr>
                <td>{{ __('From') }}:</td>
                <td><strong>{{ $email->from }}</strong></td>
            </tr>
            <tr>
                <td>{{ __('To') }}:</td>
                <td><strong>{{ $email->to }}</strong></td>
            </tr>
            <tr>
                <td>{{ __('Subject') }}:</td>
                <td><strong>{{ $email->subject }}</strong></td>
            </tr>
        </tbody>
    </table>
    <div>
        {!! $email->body !!}

        @isset($signature)
            {!! $signature !!}
        @endisset
    </div>
</body>
</html>
