@php
$item = $lead ?? $customer;
$url = isset($lead) ? 'lead' : 'customer';
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
            <div class="col"><a class="btn btn-primary btn-sm" href="/contacts/create">{{ __('New') }}</a></div>
        </div>        
    </div>
    <div class="card-body">
        @if(isset($item->contacts) && count($item->contacts) > 0)
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">{{ __('First name') }}</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($item->contacts as $contact)
                <tr>
                    <th scope="row">{{ $contact->id }}</th>
                    <td>{{ $contact->first_name }}</td>
                    <td><a class="btn btn-primary btn-sm" href="{{ url("/contact/update/$contact->id") }}">{{ __('Edit') }}</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>

@endsection
