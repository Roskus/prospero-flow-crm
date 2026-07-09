@extends('layouts.app')

@section('content')
@include('layouts.partials._header', ['title' => __('Pending Approvals')])

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover table-sm">
            <thead>
            <tr>
                <th>{{ __('Employee') }}</th>
                <th>{{ __('Type') }}</th>
                <th>{{ __('From') }}</th>
                <th>{{ __('To') }}</th>
                <th>{{ __('Days') }}</th>
                <th>{{ __('Reason') }}</th>
                <th>{{ __('Attachment') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($requests as $r)
            <tr>
                <td>{{ $r->user->first_name }} {{ $r->user->last_name }}</td>
                <td>{{ __(ucfirst($r->type)) }}</td>
                <td>{{ $r->start_date->format('Y-m-d') }}</td>
                <td>{{ $r->end_date->format('Y-m-d') }}</td>
                <td>{{ (int) $r->days_used == $r->days_used ? (int) $r->days_used : $r->days_used }}</td>
                <td>{{ Str::limit($r->reason, 50) }}</td>
                <td>
                    @if($r->attachment)
                        <a href="{{ \Illuminate\Support\Facades\Storage::url($r->attachment) }}" target="_blank" class="btn btn-xs btn-info text-white"><i class="las la-file"></i></a>
                    @endif
                </td>
                <td class="text-nowrap">
                    <form method="POST" action="{{ url("/rrhh/approvals/{$r->id}/approve") }}" style="display:inline">
                        @csrf
                        <button type="submit" class="btn btn-xs btn-success">{{ __('Approve') }}</button>
                    </form>
                    <form method="POST" action="{{ url("/rrhh/approvals/{$r->id}/reject") }}" style="display:inline" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                        @csrf
                        <button type="submit" class="btn btn-xs btn-danger">{{ __('Reject') }}</button>
                    </form>
                </td>
            </tr>
            @endforeach
            </tbody>
            </table>
            <div>{{ $requests->appends(request()->query())->links() }}</div>
        </div>
    </div>
</div>
@endsection
