@extends('layouts.app')

@section('content')
    <header>
        <h1>{{ __('Tickets') }}</h1>
    </header>

    <div class="row mb-3">
        <div class="col">
            <a href="{{ url('ticket/create') }}" class="btn btn-primary">{{ __('New') }}</a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
        <thead>
        <tr>
            <th>#ID</th>
            <th>{{ __('Title') }}</th>
            <th>{{ __('Created by') }}</th>
            <th>{{ __('Assigned to') }}</th>
            <th>{{ __('Type') }}</th>
            <th>{{ __('Priority') }}</th>
            <th>{{ __('Status') }}</th>
            <th>{{ __('Actions') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($tickets as $ticket)
        <tr>
            <td>{{ $ticket->id }}</td>
            <td>
                <a href="{{ url("ticket/update/$ticket->id") }}">{{ $ticket->title }}</a>
            </td>
            <td>{{ $ticket->createdBy->first_name.' '.$ticket->createdBy->last_name }}</td>
            <td>

            </td>
            <td>

            </td>
            <td>

            </td>
            <td>

            </td>
            <td>
                <a href="{{ url("ticket/update/$ticket->id") }}" class="btn btn-xs btn-warning text-white">
                    <i class="las la-pen"></i>
                </a>
                <a href="{{ url("ticket/delete/$ticket->id") }}" class="btn btn-xs btn-danger">
                    <i class="las la-trash-alt"></i>
                </a>
            </td>
        </tr>
        @endforeach
        </tbody>
        </table>
    </div>
@endsection
