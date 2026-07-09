@extends('layouts.app')

@section('content')
@include('layouts.partials._header', ['title' => __('Time Off Requests')])

<div class="alert alert-info">
    <div><strong>{{ __('Vacation') }}:</strong> {{ $available_vacation }} / {{ $annual_vacation }} {{ __('days available') }}</div>
    <div><strong>{{ __('Personal') }}:</strong> {{ $available_personal }} / {{ $annual_personal }} {{ __('days available') }}</div>
</div>

<div class="row mb-2">
    <div class="col">
        <a href="{{ url('/rrhh/time-off/create') }}" class="btn btn-primary">{{ __('New Request') }}</a>
    </div>
    <div class="col">
        <form method="get" action="{{ url('/rrhh/time-off') }}" class="form-inline">
            <div class="input-group">
                <select name="status" class="form-select">
                    <option value="">{{ __('All') }}</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>{{ __('Approved') }}</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                </select>
                <button class="btn btn-outline-primary" type="submit"><i class="las la-filter"></i></button>
            </div>
        </form>
    </div>
</div>

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
                <th>{{ __('Attachment') }}</th>
                <th>{{ __('Status') }}</th>
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
                <td>
                    @if($r->attachment)
                        <a href="{{ \Illuminate\Support\Facades\Storage::url($r->attachment) }}" target="_blank" class="btn btn-xs btn-info text-white"><i class="las la-file"></i></a>
                    @endif
                </td>
                <td><span class="badge bg-{{ $r->status == 'approved' ? 'success' : ($r->status == 'rejected' ? 'danger' : 'warning') }}">{{ __(ucfirst($r->status)) }}</span></td>
                <td>
                    @if($r->status == 'pending' && (Auth::user()->can('approve timeoff') || $r->user->manager_id == Auth::user()->id))
                    <a href="{{ url('/rrhh/approvals') }}" class="btn btn-xs btn-success">{{ __('Approve') }}</a>
                    @endif
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
