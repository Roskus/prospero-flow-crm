@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', ['title' => __('Emails')])
    <div class="card">
        <div class="card-body">
            <form method="post" action="{{ url('/email/save') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col">
                        <label for="subject" class="">{{ __('Subject') }}</label>
                        <input type="text" name="subject" id="subject" maxlength="80" required
                            value="{{ $email->subject }}" class="form-control">
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label for="from" class="">{{ __('From') }}</label>
                        <select name="from" id="from" required class="form-select">
                            <option value=""></option>
                            @foreach ($froms as $from)
                                <option value="{{ $from }}"
                                    @if ($from == $email->from) selected="selected" @endif>{{ $from }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label for="to" class="">{{ __('To') }}</label>
                        <input type="email" name="to" id="to" maxlength="255" required
                            value="{{ $email->to }}" class="form-control">
                    </div>
                    <div class="col">
                        <label for="cc" class="">CC</label>
                        <input type="text" name="cc" id="cc" value="{{ $email->cc }}" maxlength="255"
                            class="form-control">
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label for="body" class="">{{ __('Message') }}</label>
                        <textarea name="body" id="body">{{ $email->body }}</textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col mt-2">
                        <label for="attachments" class="">{{ __('Attachments') }}</label>
                        <input type="file" name="attachments[]" class="form-control"> <!-- accept="application/"-->
                    </div>
                </div>
                <div class="row">
                    <div class="col mt-2">
                        <a href="{{ url('/email') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>

                    </div>
                    <div class="col mt-2">
                        @isset($email->id)
                            <button data-bs-toggle="modal" data-bs-target="#duplicate-email-modal" type="button"
                                    class="btn btn-outline-dark ms-2"><i class="las la-copy"></i> {{ __('Duplicate') }}</button>
                        @endisset
                    </div>
                </div>
                <input type="hidden" name="id" value="{{ $email->id }}">
            </form>
        </div>
    </div>
    <!--./card-->

    @isset($email->id)
    <div class="modal" tabindex="-1" id="duplicate-email-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('email.duplicate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="email_id" value="{{ $email->id }}">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Duplicate') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="{{ __('Close') }}"></button>
                    </div>
                    <div class="modal-body">
                        <label for="email_to" class="form-label">
                            {{ __('Email') }} <span class="text-danger">*</span>
                        </label>
                        <input type="email" name="email_to" id="email_to" required class="form-control">
                        <div class="form-text">
                            {{ __('Please enter the recipient\'s email.') }}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('Close') }}
                        </button>
                        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--./modal-->
    @endisset

    @include('html_editor', ['id' => 'body', 'placeholder' => 'Email body message'])
@endsection
