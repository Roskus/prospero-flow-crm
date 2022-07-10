@extends('layouts.app')

@section('content')
    <header>
        <h1>{{ __('Leads') }}
    </header>
    <form method="POST" action="/lead/save" class="form">
        {{ csrf_field() }}
        <div class="row">
            <div class="col">
                <label>{{ __('Name') }} <span class="text-danger">*</span></label>
                <input type="text" name="first_name" value="{{ $lead->first_name }}" required="required" class="form-control form-control-lg">
            </div>
            <div class="col">
                <label>{{ __('Business name') }}</label>
                <input type="text" name="last_name" value="{{ $lead->last_name }}" class="form-control form-control-lg">
            </div>
        </div>
        <div class="row">
            <div class="col">
                <label>{{ __('Phone') }}</label>
                <input type="tel" name="phone" id="phone" value="{{ $lead->phone }}" maxlength="15" class="form-control form-control-lg">
            </div>
            <div class="col">
                <label>E-mail</label>
                <input type="email" name="email" id="email" value="{{ $lead->email }}" maxlength="254" class="form-control form-control-lg">
            </div>
        </div>
        <div class="row">
            <div class="col">
                <label>Website</label>
                <input type="url" name="website" id="website" value="{{ $lead->website }}" class="form-control form-control-lg">
            </div>
            <div class="col">
                <label>{{ __('Country') }} <span class="text-danger">*</span></label>
                <select name="country_id" id="country_id" required class="form-control form-control-lg">
                    <option value=""></option>
                    @foreach ($countries as $country)
                    <option value="{{ $country->code_2 }}" @if($lead->country_id == $country->code_2) selected="selected" @endif>{{ $country->name }} {{ $country->flag }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <label>{{ __('Notes') }}</label>
                <textarea name="notes" id="notes" class="form-control form-control-lg">{{ $lead->notes }}</textarea>
            </div>
        </div>
        <div class="row">
            <div class="col mt-2">
                <a href="/lead" class="btn btn-lg btn-outline-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-lg btn-primary">{{ __('Save') }}</button>
            </div>
        </div>
        <input type="hidden" name="id" value="{{ (!empty($lead)) ? $lead->id : '' }}">
    </form>

    @if($lead->id)
    <div class="mt-2">
        <h2>{{ __('Contacts') }}</h2>
    </div>
    <form method="post" action="/contact/save">
        <input type="hidden" name="lead_id" value="{{ $lead->id }}">
        <div class="row">
            <div class="col">
                <label>{{ __('First name') }}</label>
                <input type="text" name="contact_first_name" class="form-control">
            </div>
            <div class="col">
                <label>{{ __('Last name') }}</label>
                <input type="text" name="contact_last_name" class="form-control">
            </div>
        </div>
        <div class="row">
            <div class="col">
                <label>{{ __('Phone') }}</label>
                <input type="text" name="contact_phone" class="form-control">
            </div>
            <div class="col">
                <label>E-mail</label>
                <input type="text" name="contact_email" class="form-control">
            </div>
        </div>
        <div class="row">
            <div class="col mt-1">
                <button type="submit" class="btn btn-secondary">{{ __('Save') }}</button>
            </div>
        </div>
    </form>
    @endif

    <div class="mt-2">
        <table class="table table-bordered table-hover table-striped table-sm">
        <thead>
        <tr>
            <th>{{ __('First name') }}</th>
            <th>{{ __('Last name') }}</th>
            <th>{{ __('Phone') }}</th>
            <th>E-mail</th>
            <th>Linkedin</th>
            <th>{{ __('Created at') }}</th>
            <th>{{ __('Updated at') }}</th>
        </tr>
        </thead>
        <tbody>
        @if($lead->contacts)
            @foreach($lead->contacts as $contact)
            <tr>
                <td>{{ $contact->first_name }}</td>
                <td>{{ $contact->first_name }}</td>
                <td>
                    @if($contact->phone)
                    <a href="https://api.whatsapp.com/send/?phone={{ $contact->phone }}&text={{ __('Hello') }}">{{ $contact->phone }}</a>
                    @endif
                </td>
                <td>
                    @if($contact->email)
                    <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                    @endif
                </td>
                <td>
                    @if($contact->linkedin)
                    <a href="{{ $contact->linkedin }}">{{ $contact->linkedin }}</a>
                    @endif
                </td>
            </tr>
            @endforeach
        @else
            <tr>
                <td colspan="4">{{ __('No contacts') }}</td>
            </tr>
        @endif
        </tbody>
        </table>
    </div>

    @push('scripts')
        <script>
            $('#phone').on('paste', function() {
                let $el = $(this);
                setTimeout(function() {
                    $el.val(function(i, val) {
                        return val.replace(/[ ()-.]/g, '')
                    })
                })
            });

            $('#email').on('paste', function() {
                let $el = $(this);
                setTimeout(function() {
                    $el.val(function(i, val) {
                        return val.replace('mailto:', '').trim()
                    })
                })
            });
        </script>
    @endpush
@endsection
