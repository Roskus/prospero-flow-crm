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
            <caption></caption>
            <thead>
            <tr>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Subject') }}</th>
                <th>{{ __('From') }}</th>
                <th>{{ __('Recipients') }}</th>
                <th>{{ __('Tags') }}</th>
                <th>{{ __('Created at') }}</th>
                <th>{{ __('Updated at') }}</th>
                <th>{{ __('Schedule at') }}</th>
                <th>{{ __('Delivered at') }}</th>
                <th>{{ __('Emails sent') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($campaigns as $campaign)
            <tr>
                <td>
                    <a href="{{ url("campaign/update/$campaign->id") }}">{{ $campaign->name }}</a>
                </td>
                <td>{{ $campaign->subject }}</td>
                <td>{{ $campaign->from }}</td>
                <td>
                    {{ $recipient_labels[$campaign->recipient_type] ?? __('Leads') }}
                    <div class="small text-muted">
                        {{ $campaign->audience === \App\Models\Campaign::AUDIENCE_TAGS ? __('Only those with any of these tags') : __('Everyone with opt in') }}
                    </div>
                </td>
                <td>
                    @if($campaign->tags)
                        @foreach($campaign->tags as $tag)
                            <a href="{{ url("/lead?search=$tag") }}" class="badge {{ $bootstrap_colors[array_rand($bootstrap_colors)] }} text-decoration-none">{{ $tag }}</a>
                        @endforeach
                    @endif
                </td>
                <td>{{ $campaign->created_at }}</td>
                <td>{{ $campaign->updated_at }}</td>
                <td>{{ $campaign->schedule_send_date }} {{ $campaign->schedule_send_time }}</td>
                <td>{{ (isset($campaign->send_at)) ? $campaign->send_at : '-' }}</td>
                <td>{{ (isset($campaign->emails_count)) ? $campaign->emails_count : '-'  }}</td>
                <td>
                    @switch($campaign->status())
                        @case(\App\Models\Campaign::STATUS_SENT)
                            <span class="badge text-bg-success">{{ __('Sent') }}</span>
                            @break
                        @case(\App\Models\Campaign::STATUS_SCHEDULED)
                            <span class="badge text-bg-primary">{{ __('Scheduled') }}</span>
                            @break
                        @default
                            <span class="badge text-bg-warning">{{ __('Draft') }}</span>
                    @endswitch
                </td>
                <td>
                    <a href="{{ url("/campaign/update/$campaign->id") }}" title="{{ __('Update') }}" class="btn btn-xs btn-warning text-white">
                        <i class="las la-pen"></i>
                    </a>
                    @can('create campaign')
                        <form method="post" action="{{ url("/campaign/$campaign->id/clone") }}" class="d-inline">
                            @csrf
                            <button type="submit" title="{{ __('Duplicate') }}" class="btn btn-xs btn-secondary">
                                <i class="las la-copy"></i>
                            </button>
                        </form>
                    @endcan
                    @can('delete campaign')
                        <form method="post" action="{{ url("/campaign/$campaign->id") }}" class="d-inline"
                              onsubmit="return confirm(@js(__('Are you sure you want to delete the campaign?')));">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="{{ __('Delete') }}" class="btn btn-xs btn-danger">
                                <i class="las la-trash-alt"></i>
                            </button>
                        </form>
                    @endcan
                </td>
            </tr>
            @endforeach
            </tbody>
            </table>
        </div>
    </div>
@endsection
