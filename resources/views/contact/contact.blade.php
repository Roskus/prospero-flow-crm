@extends('layouts.app')

@section('content')
@include('layouts.partials._header', ['title' =>  __('Contact')])

<div class="card">
    <div class="card-body">
        <form method="post" action="{{ url('/contact/save') }}">
            @csrf
            <input type="hidden" name="id" value="{{ !empty($contact) ? $contact->id : '' }}">
            <input type="hidden" name="lead_id" value="{{ !empty($contact) ? $contact->lead_id : '' }}">
            <input type="hidden" name="customer_id" value="{{ !empty($contact) ? $contact->customer_id : '' }}">
            <div class="row">
                <div class="col">
                    <label for="contact_first_name">{{ __('First name') }}</label>
                    <input type="text" name="contact_first_name" id="contact_first_name" value="{{ !empty($contact) ? $contact->first_name : '' }}" required class="form-control">
                </div>
                <div class="col">
                    <label for="contact_last_name">{{ __('Last name') }}</label>
                    <input type="text" name="contact_last_name" id="contact_last_name" value="{{ !empty($contact) ? $contact->last_name : '' }}" class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="contact_phone">{{ __('Phone') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="las la-phone"></i></span>
                        <input type="tel" name="contact_phone" id="contact_phone" value="{{ !empty($contact) ? $contact->phone : '' }}" maxlength="15" class="form-control">
                    </div>
                </div>
                <div class="col">
                    <label for="contact_mobile">{{ __('Mobile') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="las la-mobile"></i></span>
                        <input type="tel" name="contact_mobile" id="contact_mobile" value="{{ !empty($contact) ? $contact->mobile : '' }}" maxlength="15" class="form-control">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="contact_email">E-mail</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="las la-envelope"></i></span>
                        <input type="email" name="contact_email" id="contact_email" value="{{ !empty($contact) ? $contact->email : '' }}" maxlength="254" class="form-control">
                    </div>
                </div>
                <div class="col">
                    <label for="contact_linkedin">Linkedin</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="lab la-linkedin-in"></i></span>
                        <input type="url" name="contact_linkedin" id="contact_linkedin" placeholder="https://linkedin.com/in/" value="{{ !empty($contact) ? $contact->linkedin : '' }}" class="form-control">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="contact_job_title">{{ __('Job title') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="las la-briefcase"></i></span>
                        <input type="text" name="contact_job_title" id="contact_job_title" value="{{ !empty($contact) ? $contact->job_title : '' }}" maxlength="80" class="form-control">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="contact_notes">{{ __('Notes') }}</label>
                    @php
                    $notes = !empty($contact) ? $contact->notes : '';
                    @endphp
                    <textarea name="contact_notes" id="contact_notes" class="form-control">{{ $notes }}</textarea>
                </div>
            </div>
            <div class="row">
                <div class="col mt-2">
                    <button type="submit" class="btn btn-secondary">{{ __('Save') }}</button>
                    @php
                        $url = $contact->lead_id ? 'lead/show/' . $contact->lead_id : 'customer/show/' . $contact->customer_id;
                    @endphp
                    <a href="{{ url($url) }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
