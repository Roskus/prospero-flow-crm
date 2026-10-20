@extends('layouts.app')

@section('content')
@include('layouts.partials._header', ['title' => __('Users')])
@include('layouts.partials._errors')

@can('create user')
<div class="row mb-3">
    <div class="col">
        <a href="{{ url('/user/create') }}" class="btn btn-primary">{{ __('New') }}</a>
    </div>
</div>
@endcan

@role('SuperAdmin')
<p class="text-muted mb-2" id="multi-company-summary">
    <i class="las la-building"></i>
    {{ __('Users with access to several companies: :count', ['count' => $multiCompanyUsersCount]) }}
    &middot;
    {{ __('MultiCompanyAdmin: :count', ['count' => $multiCompanyAdminsCount]) }}
</p>
@endrole

<div class="row mt-2">
    <div class="col">
        <form method="get" action="{{ url("/user") }}" class="form-inline mb-2">
            @csrf
            <div class="input-group">
                <input type="search" name="search" placeholder="{{ __('Search') }}" value="{{ !empty($search) ? $search : '' }}" class="form-control">
                <button class="btn btn-outline-primary" type="submit" id="btn-search">
                    <i class="las la-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
            <caption>{{ __('User list') }}</caption>
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
                @if(Auth::user()->hasRole('SuperAdmin') || Auth::user()->isCompanyAdmin())
                <th>
                    <a href="{{ url()->current() }}?sort=company">{{ __('Company') }}</a>
                </th>
                @endif
                <th>{{ __('Roles') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Updated at') }}</th>
                <th>{{ __('Last login') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
            <tr>
                <td>
                    @if(Auth::user()->can('update user') && Auth::user()->canManageUser($user))
                        <a href="{{ url("/user/update/$user->id") }}">{{ $user->last_name }}</a>
                    @else
                        {{ $user->last_name }}
                    @endif
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
                @if(Auth::user()->hasRole('SuperAdmin') || Auth::user()->isCompanyAdmin())
                <td>
                    {{ ($user->defaultCompany ?? $user->company)?->name }}
                    @if($user->extraCompanies->isNotEmpty())
                        <small class="d-block text-muted user-extra-companies">
                            {{ __('Also has access to: :companies', ['companies' => $user->extraCompanies->pluck('name')->implode(', ')]) }}
                        </small>
                    @endif
                </td>
                @endif
                <td>
                    {{ $user->getRoleNames()->map(fn ($role) => __($role))->implode(', ') }}
                </td>
                <td class="text-center">
                    @if($user->active)
                        <span class="badge bg-success">{{ __('Active') }}</span>
                    @else
                        <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                    @endif
                </td>
                <td>{{ $user->updated_at->format('d/m/Y H:i') }}</td>
                <td>{{ (!empty($user->last_login_at)) ? $user->last_login_at->diffForHumans() : '' }}</td>
                <td class="text-nowrap">
                    @if(Auth::user()->can('update user') && Auth::user()->canManageUser($user))
                    <a href="{{ url("/user/update/$user->id") }}" class="btn bt-xs btn-warning text-white">
                        <i class="las la-pen"></i>
                    </a>
                        @if($user->id !== Auth::id())
                        <form method="POST" action="{{ url("/user/status/$user->id") }}" class="d-inline"
                              onsubmit="return confirm('{{ $user->active ? __('Deactivate this user? They will not be able to log in.') : __('Activate this user?') }}')">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="active" value="{{ $user->active ? 0 : 1 }}">
                            <button type="submit" class="btn bt-xs {{ $user->active ? 'btn-secondary' : 'btn-success' }}"
                                    title="{{ $user->active ? __('Deactivate') : __('Activate') }}">
                                <i class="las {{ $user->active ? 'la-user-slash' : 'la-user-check' }}"></i>
                            </button>
                        </form>
                        @endif
                    @endif

                    @can('delete user')
                    <a href="#" onclick="User.delete({{ $user->id }},'{{ __('Are you sure you want to delete the user: :name?', ['name' => e($user->first_name).' '.e($user->last_name)]) }}')" class="btn bt-xs btn-danger">
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

