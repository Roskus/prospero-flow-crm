@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', ['title' => $customer->name])

    <div class="card mb-2">
        <div class="card-body">
            <div class="row">
                <div class="col-md-2">{{ __('Name') }}: <strong>{{ $customer->name }}</strong></div>
                <div class="col-md-2">{{ __('Business Name') }}: <strong>{{ $customer->business_name }}</strong></div>
                <div class="col-md-2">{{ __('Phone') }}: <strong>{{ $customer->phone }}</strong></div>
                <div class="col-md-2">{{ __('E-mail') }}: <strong>{{ $customer->email }}</strong></div>
                <div class="col-md-2"><a class="btn btn-primary btn-sm"
                        href="{{ url("/customer/update/$customer->id") }}">{{ __('Edit') }} {{ __('Customer') }}</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-2">
        <div class="card-header">{{ __('Orders') }}</div>
        <div class="card-body">
            {{-- REUSAR LA TABLA DEL ORDER.INDEX --}}
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
                    @foreach ($customer->orders as $order)
                        <tr>
                            <th scope="row">{{ $order->id }}</th>
                            <td>{{ $order->amount }}</td>
                            <td>{{ $order->status }}</td>
                            <td>{{ $order->created_at->format('d/m/Y') }}</td>
                            <td><a class="btn btn-primary btn-sm"
                                    href="{{ url("/order/update/$order->id") }}">{{ __('Edit') }}</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
