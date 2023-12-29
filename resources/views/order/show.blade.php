@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', ['title' =>  __('Order').' #'.$order->orderNumber(), 'print' => true])
    @include('order.partial._order', ['order' =>  $order])

    <div class="mt-2 text-center d-print-none">
        <a href="{{ route('order-download', ['id' => $order->id]) }}" download="file" class="btn btn-lg btn-success">
            <i class="las la-file-pdf"></i> {{ __('Download') }}
        </a>
    </div>
@endsection
