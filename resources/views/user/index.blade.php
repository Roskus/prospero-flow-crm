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

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
            <thead>
            <tr>
                <th>
                    <a href="{{ url()->current() }}?sort=last_name">{{ __('Last name') }}</a>
                </th>
                <th>
                    <a href="{{ url()->current() }}?sort=first_name">{{ __('First name') }}</a>
                </th>
                <th>E-mail</th>
                <th>{{ __('Phone') }}</th>
                <th>{{ __('Language') }}</th>
                @if(\Illuminate\Support\Facades\Auth::user()->hasRole('SuperAdmin'))
                <th>
                    <a href="{{ url()->current() }}?sort=company">{{ __('Company') }}</a>
                </th>
                @endif
                <th>{{ __('Roles') }}</th>
                <th>{{ __('Updated at') }}</th>
                <th>{{ __('Last login') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
            <tr>
                <td>
                    @can('update user')
                        <a href="{{ url("/user/update/$user->id") }}">{{ $user->last_name }}</a>
                    @else
                        {{ $user->last_name }}
                    @endcan
                </td>
                <td>{{ $user->first_name }}</td>
                <td>
                    <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                </td>
                <td>
                    @if($user->phone)
                        <a href="tel:{{ $user->phone }}">{{ $user->phone }}</a>
                    @endif
                </td>
                <td class="text-center">{{ $user->lang }}</td>
                @if(\Illuminate\Support\Facades\Auth::user()->hasRole('SuperAdmin'))
                <td>{{ $user->company->name }}</td>
                @endif
                <td>
                    {{ $user->getRoleNames() }}
                </td>
                <td>{{ $user->updated_at->format('d/m/Y H:i') }}</td>
                <td>{{ (!empty($user->last_login_at)) ? $user->last_login_at->diffForHumans() : '' }}</td>
                <td class="text-nowrap">
                    @can('update user')
                    <a href="{{ url("/user/update/$user->id") }}" class="btn bt-xs btn-warning text-white">
                        <i class="las la-pen"></i>
                    </a>
                    @endcan

                    @can('delete user')
                    <a href="#" onclick="User.delete({{ $user->id }},'{{ __('Are you sure you want to delete the user: :name?', ['name' => $user->first_name.' '.$user->last_name ]) }}')" class="btn bt-xs btn-danger">
                        <i class="las la-trash-alt"></i>
                    </a>
                    @endcan
                </td>
            </tr>
            @endforeach
            </tbody>
            </table>
        </div>
    </div>
</div><!--./card-->
<div class="mt-1">
    {{ $users->appends(request()->query())->links() }}
</div>
@push('scripts')
    <script src="{{ asset('/asset/js/User.js') }}"></script>
@endpush
@endsection

