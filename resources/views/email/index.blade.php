@extends('layouts.app')

@section('content')
    <header>
        <h1>E-mail</h1>
    </header>

    <div class="row">
        <div class="col">
            <a href="{{ url('/email/create') }}" class="btn btn-primary">{{ __('New') }}</a>
        </div>

        <div class="col">
            <form method="GET" action="{{ url('/email') }}">
                <div class="input-group">
                    <input name="search" type="search" class="form-control" placeholder="{{ __('Search') }}" value="{{ request()->get('search') }}">
                    <button class="btn btn-outline-secondary" type="submit" id="button-search"><i class="las la-search"></i></button>
                </div>
            </form>
        </div>
    </div>    

    <div class="mt-2">
    <table class="table table-bordered table-striped">
    <thead>
    <tr>
        <td>{{ __('Subject') }}</td>
        <td>{{ __('To') }}</td>
        <td>Updated at</td>
        <td>{{ __('Status') }}</td>
        <td>{{ __('Actions') }}</td>
    </tr>
    </thead>
    <tbody>
    @foreach($emails as $email)
    <tr>
        <td>
            <a href="{{ url("/email/update/$email->id") }}">{{ $email->subject }}</a>
        </td>
        <td>{{ $email->to }}</td>
        <td>{{ $email->updated_at->format('d/m/Y H:i') }}</td>
        <td class="text-center">
            {{ $email->status }}
        </td>
        <td>
            <a href="{{ url("/email/update/$email->id") }}" class="btn btn-warning" title="{{ __('Edit') }}">
                <i class="las la-edit"></i>
            </a>
            @if($email->status != \App\Models\Email::SENT)
            <a href="{{ url("/email/send/$email->id") }}" class="btn btn-primary">
                <i class="las la-envelope"></i>
            </a>
            <a href="{{ url("/email/delete/$email->id") }}" class="btn btn-danger">
                <i class="las la-trash"></i>
            </a>
            @else
                <a href="{{ url("/email/archive/$email->id") }}" class="btn btn-success" title="{{ __('Archive') }}">
                    <i class="las la-trash"></i>
                </a>
            @endif
        </td>
    </tr>
    @endforeach
    </tbody>
    </table>
    </div>
@endsection
