@php
$item = $lead ?? $customer;
$url = isset($lead) ? 'lead' : 'customer';

// TODO improve this
$lead_id =  isset($lead) ?? $lead->id;
$customer_id =  isset($customer) ?? $customer->id;
@endphp

@extends('layouts.app')

@section('content')
@include('layouts.partials._header', ['title' => $item->name])

<div class="card mb-2">
    <div class="card-body">
        <div class="row">
            <div class="col-md-2">
                <div>{{ __('Name') }}:</div>
                <strong>{{ $item->name }}</strong>
            </div>
            <div class="col-md-2">
                <div>{{ __('Business name') }}:</div>
                <strong>{{ $item->business_name }}</strong>
            </div>
            <div class="col-md-2">
                <div>{{ __('Phone') }}:</div>
                <strong>{{ $item->phone }}</strong>
            </div>
            <div class="col-md-2">
                <div>{{ __('E-mail') }}:</div>
                <strong>{{ $item->email }}</strong>
            </div>
            <div class="col-md-3">
                <div>{{ __('Identity number') }}:</div>
                <strong>{{ $item->vat }}</strong>
            </div>
            <div class="col-md-1">
                <a class="btn btn-warning" href="{{ url("/$url/update/$item->id") }}">
                    <i class="las la-pen"></i> {{ __('Edit') }}
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ORDERS --}}
@if($url === 'customer')
<div class="card mb-2">
    <div class="card-header">{{ __('Orders') }}</div>
    <div class="card-body">
        @if(isset($item->orders) && count($item->orders) > 0)
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">{{ __('Amount') }}</th>
                    <th scope="col">{{ __('Status') }}</th>
                    <th scope="col">{{ __('Created at') }}</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($item->orders as $order)
                <tr>
                    <th scope="row">{{ $order->id }}</th>
                    <td>{{ $order->amount }}</td>
                    <td>{{ $order->status }}</td>
                    <td>{{ $order->created_at->format('d/m/Y') }}</td>
                    <td><a class="btn btn-primary btn-sm" href="{{ url("/order/update/$order->id") }}">{{ __('Edit') }}</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
@endif

{{-- CONTACTS --}}
<div class="card mb-2">
    <div class="card-header">
        <div class="row">
            <div class="col">{{ __('Contacts') }}</div>
            <div class="col">
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#formContactModal">{{ __('New') }}</button>
            </div>
        </div>
    </div>
    <div class="card-body">
        @if(isset($item->contacts) && count($item->contacts) > 0)
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">{{ __('First name') }}</th>
                    <th scope="col">{{ __('Phone') }}</th>
                    <th scope="col">E-mail</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($item->contacts as $contact)
                <tr>
                    <th scope="row">{{ $contact->id }}</th>
                    <td>{{ $contact->first_name }}</td>
                    <td>{{ $contact->phone }}</td>
                    <td>{{ $contact->email }}</td>
                    <td>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#formContactModal" data-bs-id="{{ $contact->id }}">{{ __('Edit') }}</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>

<div class="modal fade" id="formContactModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Contact') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formContact" method="POST" action="{{ url('/api/contact') }}">
                    @include('contact.partials._form_fields')
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button id="btnSaveForm" type="button" class="btn btn-primary">{{ __('Save') }}</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script>
        var api_token = "";
    </script>
    <script src="{{ asset('asset/js/ContactModalForm.js') }}"></script>
@endpush
