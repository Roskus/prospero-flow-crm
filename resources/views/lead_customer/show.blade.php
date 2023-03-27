@php
if(empty($customer)) $customer = null;
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
                @isset($item->phone)
                    <strong>
                        <a href="tel:{{ $item->phone }}">{{ \App\Helpers\PhoneHelper::format($item->phone) }}</a>
                    </strong>
                @endisset
            </div>
            <div class="col-md-2">
                <div>{{ __('E-mail') }}:</div>
                @isset($item->email)
                    <strong>
                        <a href="mailto:{{ $item->email }}">{{ $item->email }}</a>
                    </strong>
                @endisset
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
@php
    $orders = $item->orders
@endphp
<div class="card mb-2">
    <div class="card-header">{{ __('Products') }}</div>
    <div class="card-body">
        @if(isset($orders) && count($orders) > 0)
            <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">{{ __('Product') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($orders as $order)
                @foreach($order->items as $item_detail)
                    @isset($item_detail->product)
                    <tr>
                    <td>{{ $item_detail->product->id }}</td>
                    <td>
                        @isset($item_detail->product->photo)
                        <div>
                        <img src="{{ App\Helpers\ImageHelper::render(public_path('/asset/upload/product/'.$item_detail->product->id.'/'.$item_detail->product->photo)) }}" alt="" width="100" class="img-fluid img-thumbnail">
                        </div>
                        @endif
                        {{ $item_detail->product->name }}
                    </td>
                    </tr>
                    @endisset
                @endforeach
            @endforeach
            </tbody>
            </table>
        @endif
    </div>
</div>

<div class="card mb-2">
    <div class="card-header">{{ __('Orders') }}</div>
    <div class="card-body">
        @if(isset($orders) && count($orders) > 0)
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
                @foreach ($orders as $order)
                <tr>
                    <th scope="row">{{ $order->id }}</th>
                    <td>{{ $order->amount }}</td>
                    <td>{{ $order->status }}</td>
                    <td>{{ $order->created_at->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ url('/order/show/'.$order->id) }}" title="{{ __('View') }}"
                           class="btn btn-sm btn-primary text-white">
                            {{ __('View') }}
                        </a>

                        <a href="{{ url("/order/update/$order->id") }}" class="btn btn-primary btn-sm">
                            {{ __('Edit') }}
                        </a>
                    </td>
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
            <div class="col">
                {{ __('Contacts') }}

                <a class="btn btn-primary btn-sm" style="border-radius: 40px;" href="{{ url('/contact/create', [$url, $item->id]) }}">
                    <i class="las la-plus fw-bold"></i>
                </a>
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
                    <th scope="col">{{ __('Mobile') }}</th>
                    <th scope="col">E-mail</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($item->contacts as $contact)
                <tr>
                    <th scope="row">{{ $contact->id }}</th>
                    <td>{{ $contact->first_name }}</td>
                    <td>
                        <a href="tel:{{ $contact->phone }}">{{ $contact->phone }}</a>
                    </td>
                    <td>{{ $contact->mobile }}</td>
                    <td>{{ $contact->email }}</td>
                    <td>
                        <a href="{{ url('/contact/update/'.$contact->id) }}" class="btn btn-warning btn-sm">
                            {{ __('Edit') }}
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>

@endsection
