@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', ['title' =>  __('E-mail')])
    @include('layouts.partials._search_buttons_bar', [
        'action_search' => url("/email"), 
        'buttons' => [
            ['url' => url('/email/create'), 'class' => 'btn btn-primary', 'text' => __('New')]
        ]
    ])

    <div class="card mt-2">
        <div class="card-body">
            <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <td>{{ __('Subject') }}</td>
                <td>{{ __('From') }}</td>
                <td>{{ __('To') }}</td>
                <td class="text-center">
                    <i class="las la-paperclip"></i>
                </td>
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
                <td colspan="no-wrap">
                    <a href="{{ url("/email/view/$email->id") }}" title="{{ __('Preview') }}" target="_blank" class="btn btn-secondary">
                        <i class="las la-glasses"></i>
                    </a>

                    @if($email->status != \App\Models\Email::SENT)
                    <a href="{{ url("/email/update/$email->id") }}" title="{{ __('Edit') }}" class="btn btn-warning text-white">
                        <i class="las la-edit"></i>
                    </a>
                    @endif

                    <a onclick="window.ProspectFlow.Email.duplicate({{ $email->id, __('New recipient') }})" title="{{ __('Duplicate') }}" class="btn btn-info text-white">
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
    @include('email.popup.duplicate')
    @push('scripts')
        <script src="/asset/js/Email.js"></script>
    @endpush
@endsection
