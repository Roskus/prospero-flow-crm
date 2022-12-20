@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', ['title' =>  __('Tickets')])

    <div class="row mb-3">
        <div class="col">
            <a href="{{ url('ticket/create') }}" class="btn btn-primary">{{ __('New') }}</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th>#ID</th>
                    <th>{{ __('Title') }}</th>
                    <th>{{ __('Customer') }}</th>
                    <th>{{ __('Created by') }}</th>
                    <th>{{ __('Assigned to') }}</th>
                    <th>{{ __('Order') }}</th>
                    <th>{{ __('Type') }}</th>
                    <th>{{ __('Priority') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Created at') }}</th>
                    <th>{{ __('Updated at') }}</th>
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
                    <td>{{ (!empty($ticket->customer)) ? $ticket->customer->name : '' }}</td>
                    <td>{{ $ticket->createdBy->first_name.' '.$ticket->createdBy->last_name }}</td>
                    <td>

                    </td>
                    <td>

                    </td>
                    <td>

                    </td>
                    <td class="text-center">
                        {{ $ticket->priority }}
                    </td>
                    <td>

                    </td>
                    <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ (!empty($ticket->updated_at)) ? $ticket->updated_at->format('d/m/Y H:i') : '' }}</td>
                    <td class="">
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
        </div>
    </div>
@endsection
