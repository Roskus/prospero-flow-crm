@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', ['title' =>  __('Banks')])
<div>
    <a href="{{ url('bank/create') }}" class="btn btn-primary">{{ __('New') }}</a>
</div>
<div class="card">
    <div class="card-body">
        <table class="table">
        <thead>
        <tr>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Country') }}</th>
            <th>{{ __('Phone') }}</th>
            <th>Email</th>
            <th>Website</th>
        </tr>
        </thead>
        <tbody>
        @foreach($banks as $bank)
        <tr>
            <td>
                <a href="/bank/update/{{ $bank->id }}">{{ $bank->name }}</a>
            </td>
            <td>{{ $bank->country }}</td>
            <td>{{ $bank->phone }}</td>
            <td>{{ $bank->email }}</td>
            <td>{{ $bank->website }}</td>
        </tr>
        @endforeach
        </tbody>
        </table>
    </div>
</div>
@endsection
