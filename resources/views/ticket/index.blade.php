@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', ['title' =>  __('Tickets')])
    @include('layouts.partials._search_buttons_bar', [
        'action_search' => url("/ticket"), 
        'buttons' => [
            ['url' => url('/ticket/create'), 'class' => 'btn btn-primary', 'text' => __('New')],
        ]
    ])

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
                    <td>{{ $ticket->assignedTo->first_name.' '.$ticket->assignedTo->last_name }}</td>
                    <td class="text-center">
                        @if($ticket->order_id)
                            <a href="{{ url("/order/update/$ticket->order_id") }}">{{ $ticket->order_id }}</a>
                        @endif
                    </td>
                    <td class="text-center">{{ __(ucfirst($ticket->type)) }}</td>
                    <td class="text-center">
                        <div class="badge rounded-pill text-bg-{{ \App\Helpers\TicketPriorityHelper::priorityCssClass($ticket->priority) }}">
                            {{ __(ucfirst($ticket->priority)) }}
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="badge">{{ __(ucfirst($ticket->status)) }}</div>
                    </td>
                    <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ (!empty($ticket->updated_at)) ? $ticket->updated_at->format('d/m/Y H:i') : '' }}</td>
                    <td class="no-wrap">
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
