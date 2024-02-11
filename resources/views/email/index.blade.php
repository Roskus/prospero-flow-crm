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
            <div class="table-responsive">
            <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>{{ __('Subject') }}</th>
                <th>{{ __('From') }}</th>
                <th>{{ __('To') }}</th>
                <th class="text-center">
                    <i class="las la-paperclip"></i>
                </th>
                <th>{{ __('Created at') }}</th>
                <th>{{ __('Updated at') }}</th>
                <th>{{ __('Status') }}</th>
                <th class="text-nowrap">{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($emails as $email)
            <tr>
                <td>
                    @if($email->status != \App\Models\Email::SENT)
                        <a href="{{ url("/email/update/$email->id") }}">{{ $email->subject }}</a>
                    @else
                        {{ $email->subject }}
                    @endif
                </td>
                <td>{{ $email->from }}</td>
                <td>{{ $email->to }}</td>
                <td class="text-center">
                    @if($email->attachments()->count())
                    <i class="las la-paperclip"></i>
                    @endif
                </td>
                <td>{{ $email->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $email->updated_at->format('d/m/Y H:i') }}</td>
                <td class="text-center">
                    <div class="badge rounded-pill text-bg-{{ \App\Helpers\EmailStatusHelper::statusCssClass($email->status) }}">{{ __(ucfirst($email->status)) }}</div>
                </td>
                <td colspan="text-nowrap">
                    <a href="{{ url("/email/view/$email->id") }}" title="{{ __('Preview') }}" target="_blank" class="btn btn-secondary">
                        <i class="las la-glasses"></i>
                    </a>

                    @if($email->status != \App\Models\Email::SENT)
                    <a href="{{ url("/email/update/$email->id") }}" title="{{ __('Edit') }}" class="btn btn-warning text-white">
                        <i class="las la-edit"></i>
                    </a>
                    @endif

                    <a onclick="window.ProspectFlow.Email.duplicate({{ $email->id }}, '{{ __('New recipient') }}')" title="{{ __('Duplicate') }}" class="btn btn-info text-white">
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
            </div><!--./table-responsive-->

            <div>
                {{ $emails->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
    @include('email.popup.duplicate')
    @push('scripts')
        <script src="/asset/js/Email.js"></script>
    @endpush
@endsection
