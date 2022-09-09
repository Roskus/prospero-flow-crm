@extends('layouts.app')

@section('content')
    <header>
        <h1>E-mail</h1>
    </header>

    <div>
        <a href="/email/create" class="btn btn-primary">{{ __('New') }}</a>
    </div>

    <div class="mt-2">
    <table class="table table-bordered table-striped">
    <thead>
    <tr>
        <td>{{ __('Subject') }}</td>
        <td>To</td>
        <td>Updated at</td>
        <td>{{ __('Status') }}</td>
        <td>{{ __('Actions') }}</td>
    </tr>
    </thead>
    <tbody>
    @foreach($emails as $email)
    <tr>
        <td>
            <a href="/email/update/{{ $email->id }}">{{ $email->subject }}</a>
        </td>
        <td>{{ $email->to }}</td>
        <td>{{ $email->updated_at->format('d/m/Y H:i') }}</td>
        <td>

        </td>
        <td>
            <a href="/email/update/{{ $email->id }}" class="btn btn-warning"></a>
            <a href="/email/delete/{{ $email->id }}" class="btn btn-danger"></a>
            <a href="/email/send/{{ $email->id }}" class="btn btn-success"></a>
        </td>
    </tr>
    @endforeach
    </tbody>
    </table>
    </div>
@endsection
