@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', ['title' =>  __('Notifications')])
    <div class="card">
        <div class="card-body">
            <table class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
                <th>{{ __('Message') }}</th>
                <th>{{ __('Date') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($notifications as $notification)
            <tr>
                <td>
                    @isset($notification->link)
                        <a href="{{ $notification->link }}">{{ __($notification->message) }}</a>
                    @else
                        {{ __($notification->message) }}
                    @endisset
                </td>
                <td>{{ $notification->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            @endforeach
            </tbody>
            </table>

        </div><!--./card-body-->
    </div><!--./card-->
@endsection
