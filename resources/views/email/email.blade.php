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
                                    @if ($email->from == $from ) selected="selected" @endif>{{ $from }}
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
                    <div class="col">
                        <label for="signature">{{ __('Include signature') }}</label>
                        <input type="checkbox" name="signature" id="signature" value="true" @isset($email->signature)checked="checked"@endisset>
                    </div>
                </div><!--./row-->
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
                                    class="btn btn-outline-dark ms-2"><i class="las la-copy"></i> {{ __('Duplicate') }}
                            </button>

                            <a href="{{ url('/email/send/'.$email->id) }}" class="btn btn-primary">
                                <i class="las la-envelope"></i> {{ __('Send') }}
                            </a>
                        @endisset
                    </div>
                </div>
                <input type="hidden" name="id" value="{{ $email->id }}">
            </form>
        </div>
    </div>
    <!--./card-->

    @isset($email->id)
        @include('email.popup.duplicate')
    <!--./modal-->
    @endisset

    @include('html_editor', ['id' => 'body', 'placeholder' => 'Email body message'])
@endsection
