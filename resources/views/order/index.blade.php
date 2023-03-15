@extends('layouts.app')

@section('content')
@include('layouts.partials._header', ['title' =>  __('Orders')])
@include('layouts.partials._search_buttons_bar', [
    'action_search' => url("/order"),
    'buttons' => [
        ['url' => url('/order/create'), 'class' => 'btn btn-primary', 'text' => __('New')]
    ]
])

<div class="card">
    <div class="card-body">

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover table-condensed">
            <thead>
            <tr>
                <th>#ID</th>
                <th>{{ __('Customer') }}</th>
                <th>{{ __('Item count') }}</th>
                <th>{{ __('Amount') }}</th>
                <th>{{ __('Created at') }}</th>
                <th>{{ __('Updated at') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($orders as $order)
            <tr>
                <td>
                    <a href="{{ url('order/show/'.$order->id) }}">{{ $order->id }}</a>
                </td>
                <td>{{ (!empty($order->customer)) ? $order->customer->name : '' }}</td>
                <td class="text-center">
                    {{ (!empty($order->items)) ? $order->items->count() : 0 }}
                </td>
                <td>
                    {{ number_format($order->getAmount(), 2, ',', '.') }}
                </td>
                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $order->updated_at->format('d/m/Y H:i') }}</td>
                <td>{{ $order->status }}</td>
                <td>
                    <a href="{{ url('order/show/'.$order->id) }}" title="{{ __('View') }}"
                       class="btn btn-xs btn-primary text-white">
                        <i class="las la-eye"></i>
                    </a>
                    <a href="{{ url('/order/update/'.$order->id) }}" title="{{ __('Edit') }}"
                       class="btn btn-xs btn-warning text-white">
                        <i class="las la-pen"></i>
                    </a>
                    <a href="{{ url('/order/download/'.$order->id) }}" title="{{ __('Download') }}"
                       class="btn btn-xs btn-success">
                        <i class="las la-file-pdf"></i>
                    </a>
                    <a href="{{ url('/order/delete/'.$order->id) }}" title="{{ __('Delete') }}"
                       class="btn bt-xs btn-danger">
                        <i class="las la-trash-alt"></i>
                    </a>
                </td>
            </tr>
            @endforeach
            </tbody>
            </table>

            <div>
                {{ $orders->links() }}
            </div>
        </div>
    </div><!--./card-body-->
</div><!--./card-->
@endsection

