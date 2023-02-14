@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', ['title' =>  __('E-mail')])

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

    <div class="card mt-2">
        <div class="card-body">
            <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <td>{{ __('Subject') }}</td>
                <td>{{ __('To') }}</td>
                <td>{{ __('Created at') }}</td>
                <td>{{ __('Updated at') }}</td>
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
                <td>{{ $email->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $email->updated_at->format('d/m/Y H:i') }}</td>
                <td class="text-center">
                    {{ __(ucfirst($email->status)) }}
                </td>
                <td colspan="no-wrap">
                    <a href="{{ url("/email/view/$email->id") }}" title="{{ __('Preview') }}" target="_blank" class="btn btn-secondary">
                        <i class="las la-glasses"></i>
                    </a>
                    <a href="{{ url("/email/update/$email->id") }}" title="{{ __('Edit') }}" class="btn btn-warning text-white">
                        <i class="las la-edit"></i>
                    </a>
                    <a href="{{ url("/email/duplicate/$email->id") }}" title="{{ __('Duplicate') }}" class="btn btn-info text-white">
                        <i class="las la-copy"></i>
                    </a>
                    @if($email->status != \App\Models\Email::SENT)
                    <a href="{{ url("/email/send/$email->id") }}" title="{{ __('Send') }}" class="btn btn-primary">
                        <i class="las la-envelope"></i>
                    </a>
                    <a href="{{ url("/email/delete/$email->id") }}" title="{{ __('Delete') }}" class="btn btn-danger">
                        <i class="las la-trash"></i>
                    </a>
                    @else
                        <a href="{{ url("/email/archive/$email->id") }}" title="{{ __('Archive') }}" class="btn btn-success">
                            <i class="las la-trash"></i>
                        </a>
                    @endif
                </td>
            </tr>
            @endforeach
            </tbody>
            </table>

            <div>
                {{ $emails->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection
