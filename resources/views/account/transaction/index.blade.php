@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{$account->name}} Transactions</h1>

        <table class="table">
            <thead>
            <tr>
                <th>Date</th>
                <th>Amount</th>
                <th>Description</th>
                <th>Type</th>
            </tr>
            </thead>
            <tbody>
            @foreach($transactions as $transaction)
                <tr>
                    <td>{{$transaction->created_at}}</td>
                    <td>{{$transaction->amount}} {{$transaction->currency}}</td>
                    <td>{{$transaction->description}}</td>
                    <td>{{$transaction->type}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{ $transactions->links() }}
    </div>
@endsection
