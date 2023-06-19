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
                            value="{{ old('subject', $email->subject) }}" class="form-control">
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label for="from" class="">{{ __('From') }}</label>
                        <select name="from" id="from" required class="form-select">
                            <option value=""></option>
                            @foreach ($froms as $from)
                                <option value="{{ $from['email'] }}"
                                    @if ($email->from == $from['email'] ) selected="selected" @endif>
                                    {{ $from['email'] }}
                                    &lt;{{ $from['name'] }}&gt;
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label for="to" class="">{{ __('To') }}</label>
                        <input type="email" name="to" id="to" maxlength="255" required
                            value="{{ old('to', $email->to) }}" class="form-control">
                    </div>
                    <div class="col">
                        <label for="cc" class="">CC</label>
                        <input type="text" name="cc" id="cc" value="{{ old('cc', $email->cc) }}" maxlength="255"
                            class="form-control">
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label for="bcc" class="">BCC</label>
                        <input type="text" name="bcc" id="bcc" value="{{ old('bcc', $email->bcc) }}" maxlength="254"
                               class="form-control">
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label for="body" class="">{{ __('Message') }}</label>
                        <div class="mb-1">
                            <a onclick="addVariable2Editor('body', '$prospect->first_name')">
                                <span class="btn btn-outline-secondary">&#123;&#123; $prospect->first_name &#125;&#125;</span>
                            </a>
                        </div>
                        <textarea name="body" id="body">{{ old('body', $email->body) }}</textarea>
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
                        <label for="attachment" class="">{{ __('Attachments') }}</label>
                        <input type="file" id="attachment" name="attachment[]" class="form-control"> <!-- accept="application/"-->
                        @if($email->attachments()->count())
                            @foreach($email->attachments()->get() as $attach)
                                <div>
                                    <a href="{{ route('downloadAttachment', $attach->id) }}" target="_blank">{{ $attach->original_name }}</a>
                                </div>
                            @endforeach
                        @endif
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
