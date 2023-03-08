@extends('layouts.print')

@section('content')
    @push('styles')
        <link rel="stylesheet" type="text/css" media="print" href="/asset/css/print.css">
    @endpush
    @include('order.partial._order', ['order' =>  $order])
@endsection
