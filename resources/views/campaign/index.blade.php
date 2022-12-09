@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', ['title' =>  __('Campaigns')])

    <div class="row">
        <div class="col">
            <a href="{{ url('/campaign/create') }}" class="btn btn-primary">{{ __('New') }}</a>
        </div>
    </div>

    <div class="card mt-2">
        <div class="card-body">
            <table class="table table-bordered table-striped table-hover">
            <thead>
            <tr>
                <td>{{ __('Subject') }}</td>
                <td>{{ __('Created at') }}</td>
                <td>{{ __('Updated at') }}</td>
                <td>{{ __('Actions') }}</td>
            </tr>
            </thead>
            <tbody>
            @foreach($campaigns as $campaign)
            <tr>
                <td>
                    <a href="{{ url("campaign/update/$campaign->id") }}">{{ $campaign->subject }}</a>
                </td>
                <td>{{ $campaign->created_at }}</td>
                <td>{{ $campaign->updated_at }}</td>
                <td>
                    <a href="{{ url("/campaign/update/$campaign->id") }}" title="{{ __('Update') }}" class="btn btn-xs btn-warning text-white">
                        <i class="las la-pen"></i>
                    </a>
                    <a onclick="Campaign.delete({{ $campaign->id }}, '{{ $campaign->subject }}');" title="{{ __('Delete') }}" class="btn btn-xs btn-danger">
                        <i class="las la-trash-alt"></i>
                    </a>
                </td>
            </tr>
            @endforeach
            </tbody>
            </table>
        </div>
    </div>
    <script>
        const Campaign = {
            delete : function(id, name) {
                let res = confirm("{{ __('Are you sure you want to delete the campaign?') }}");
                if(res)
                    window.location = '{{ url('/campaign/delete') }}/'+id;
            }
        };
    </script>
@endsection
