@extends('layouts.app')

@section('content')
<header>
   <h1>{{ __('Users') }}</h1>
</header>

@can('create user')
<div class="row mb-3">
    <div class="col">
        <a href="{{ url('/user/create') }}" class="btn btn-primary">{{ __('New') }}</a>
    </div>
</div>
@endcan

<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover">
    <thead>
    <tr>
        <th>{{ __('Last name') }}</th>
        <th>{{ __('First name') }}</th>
        <th>E-mail</th>
        <th>{{ __('Phone') }}</th>
        <th>{{ __('Language') }}</th>
        @if(\Illuminate\Support\Facades\Auth::user()->hasRole('SuperAdmin'))
        <th>{{ __('Company') }}</th>
        @endif
        <th>{{ __('Roles') }}</th>
        <th>{{ __('Actions') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($users as $user)
    <tr>
        <td><a href="/user/edit/{{ $user->id}}">{{ $user->last_name }}</a></td>
        <td>{{ $user->first_name }}</td>
        <td>
            <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
        </td>
        <td>
            @if($user->phone)
                <a href="tel:{{ $user->phone }}">{{ $user->phone }}</a>
            @endif
        </td>
        <td>{{ $user->lang }}</td>
        @if(\Illuminate\Support\Facades\Auth::user()->hasRole('SuperAdmin'))
        <td>{{ $user->company->name }}</td>
        @endif
        <td>
            {{ $user->getRoleNames() }}
        </td>
        <td>
            @can('update user')
            <a href="{{ url("/user/update/$user->id") }}" class="btn bt-xs btn-warning text-white">
                <i class="las la-pen"></i>
            </a>
            @endcan

            @can('delete user')
            <a href="{{ url("/user/delete/$user->id") }}" class="btn bt-xs btn-danger">
                <i class="las la-trash-alt"></i>
            </a>
            @endcan
        </td>
    </tr>
    @endforeach
    </tbody>
    </table>
</div>
@endsection

