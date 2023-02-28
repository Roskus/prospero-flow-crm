@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', ['title' =>  __('Order'.' #'.$order->id), 'print' => true])
    @push('styles')
    <link rel="stylesheet" type="text/css" media="print" href="/asset/css/print.css">
    @endpush
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col">
                    @if(empty(Auth::user()->company->logo))
                        {{ Auth::user()->company->name }}
                    @else
                        <img src="/asset/upload/company/{{ \Illuminate\Support\Str::slug(Auth::user()->company->name, '_') }}/{{ Auth::user()->company->logo }}" alt="{{ env('APP_NAME') }}" class="logo">
                    @endif
                    <div>
                        <label>{{ __('Business name') }}:</label> {{ Auth::user()->company->business_name }}
                    </div>
                    <div>
                        <label>{{ __('Vat') }}:</label> {{ Auth::user()->company->vat }}
                    </div>
                    <div>
                        <label>{{ __('Phone') }}:</label> {{ Auth::user()->company->phone }}
                    </div>
                    <div class="">
                        <label>E-mail:</label> {{ Auth::user()->company->email }}
                    </div>
                </div>
                <div class="col">
                    <div>
                        <label>{{ __('Date') }}:</label> {{ $order->created_at->format('d/m/Y') }}
                    </div>
                    <div>
                        <label>{{ __('Address') }}:</label> {{ Auth::user()->company->street }}
                    </div>
                    <div>
                        <label>{{ __('Province') }}:</label> {{ Auth::user()->company->province }}
                    </div>
                    <div>
                        <label>{{ __('City') }}:</label> {{ Auth::user()->company->city }}
                    </div>
                    <div>
                        <label>{{ __('Zipcode') }}:</label> {{ Auth::user()->company->zipcode }}
                    </div>
                    <div>
                        <label>{{ __('Seller') }}:</label>
                        {{ !empty($order->seller) ? $order->seller->first_name.' '.$order->seller->last_name : '' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-2">
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <label>{{ __('Customer') }}:</label> {{ $order->customer->name }}
                </div>
                <div class="col">
                    <label>{{ __('Business name') }}:</label> {{ $order->customer->business_name }}
                </div>
                <div class="col">
                    <label>{{ __('Vat') }}:</label> {{ $order->customer->vat }}
                </div>
            </div><!--./row-->
            <div class="row">
                <div class="col">
                    <label>{{ __('Phone') }}:</label> {{ $order->customer->phone }}
                </div>
                <div class="col">
                    <label>{{ __('Mobile') }}:</label> {{ $order->customer->mobile }}
                </div>
                <div class="col">
                    <label>E-mail:</label> {{ $order->customer->email }}
                </div>
            </div><!--./row-->
            <div class="row">
                <div class="col">
                    <label>{{ __('Province') }}:</label> {{ $order->customer->province }}
                </div>
                <div class="col">
                    <label>{{ __('City') }}:</label> {{ $order->customer->city }}
                </div>
                <div class="col">
                    <label>{{ __('Zipcode') }}:</label> {{ $order->customer->zipcode }}
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label>{{ __('Street') }}:</label> {{ $order->customer->street }}
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-2">
        <div class="card-body">
        <table class="table table-bordered table-striped table-hover">
            <thead>
            <tr>
                <th width="60%">{{ __('Product') }}</th>
                <th>{{ __('Price') }}</th>
                <th>{{ __('Quantity') }}</th>
                <th>{{ __('Subtotal') }}</th>
            </tr>
            </thead>
            <tbody id="order-items">
            @if($order->items()->count() == 0)
                <tr id="row-no-data">
                    <td colspan="4">{{ __('No items') }}</td>
                </tr>
            @else
                @foreach($order->items as $item)
                    <tr>
                        <td>{{ (!empty($item->product)) ? $item->product->name : '' }}</td>
                        <td>{{ $item->unit_price }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ ($item->unit_price * $item->quantity) }}</td>
                    </tr>
                @endforeach
            @endif
            </tbody>
            <tfoot>
            <tr>
                <th colspan="2">&nbsp;</th>
                <th class="text-right">{{ __('Total') }}</th>
                <th>
                    {{ $order->getTotal() }}
                </th>
            </tr>
            </tfoot>
        </table>
        </div>
    </div><!--./card-->
    <div class="mt-2 text-center d-print-none">
        <a href="{{ route('order-download', ['id' => $order->id]) }}" class="btn btn-lg btn-success"><i class="las la-file-pdf"></i> {{ __('Download') }}</a>
    </div>
@endsection
