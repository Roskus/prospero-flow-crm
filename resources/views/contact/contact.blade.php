@php
$lead_id = (empty($lead_id) && !empty($contact->lead_id)) ? $contact->lead_id : $lead_id;
$customer_id = (empty($customer_id) && !empty($contact->customer_id)) ? $contact->customer_id : $customer_id;
@endphp

@empty($popup)
    @extends('layouts.app')
@endempty

@section('content')
<form method="post" action="{{ url('/contact/save') }}">
    
    @include('contact.partials._form_fields')

    <div class="row">
        <div class="col mt-2">
            <button type="submit" class="btn btn-secondary">{{ __('Save') }}</button>
        </div>
    </div>

</form>
@endsection
