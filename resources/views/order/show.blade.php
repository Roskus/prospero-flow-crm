@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', ['title' =>  __('Order'), 'print' => true])
    @push('styles')
    <link rel="stylesheet" type="text/css" media="print" href="/asset/css/print.css">
    @endpush
    <div class="row">
        <div class="col">
            {{ $order->customer->name }}
        </div>
    </div>

    <div class="content">
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
    <div class="text-center d-print-none">
        <a href="{{ route('order-download', ['id' => $order->id]) }}" class="btn btn-success"><i class="las la-file-pdf"></i> {{ __('Download') }}</a>
    </div>

    </div><!--./content-->
@endsection
