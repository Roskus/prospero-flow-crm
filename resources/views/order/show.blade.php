@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', ['title' =>  __('Order').' #'.$order->orderNumber(), 'print' => true])
    @include('order.partial._order', ['order' =>  $order])

    <div class="mt-2 text-center d-print-none">
        @if((int) $order->status === \App\Models\Order::PENDING)
            @can('update order')
            <a href="{{ url('/order/update/'.$order->order_number) }}" class="btn btn-lg btn-warning text-white">
                <i class="las la-pen"></i> {{ __('Edit') }}
            </a>
            @endcan
        @endif
        <a href="{{ route('order-download', ['order_number' => $order->order_number]) }}" download="file" class="btn btn-lg btn-success">
            <i class="las la-file-pdf"></i> {{ __('Download') }}
        </a>
        @if($order->activeInvoice && ! $order->activeInvoice->isPaid())
            @can('create payment')
            <a href="{{ route('invoice.show', $order->activeInvoice->id) }}#payment-form" class="btn btn-lg btn-success">
                <i class="las la-money-bill-wave"></i> {{ __('Pay') }}
            </a>
            @endcan
        @endif
        @if($order->activeInvoice)
            @can('read invoice')
            <a href="{{ route('invoice.show', $order->activeInvoice->id) }}" class="btn btn-lg btn-outline-info">
                <i class="las la-file-invoice-dollar"></i> {{ __('Invoice') }} {{ $order->activeInvoice->fullNumber() }}
            </a>
            @endcan
        @endif
        @can('create invoice')
            @if($order->canBeInvoiced())
            <a href="{{ route('invoice.create', $order->order_number) }}" class="btn btn-lg btn-info text-white">
                <i class="las la-file-invoice-dollar"></i> {{ __('Create invoice') }}
            </a>
            @endif
        @endcan
    </div>
@endsection
