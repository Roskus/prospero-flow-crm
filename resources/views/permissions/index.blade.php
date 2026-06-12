@extends('layouts.app')

@section('content')
@include('layouts.partials._header', ['title' => __('Permissions')])

<div class="card">
    <div class="card-body">
        <form action="{{ url('/permission') }}" method="POST">
            @csrf
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th><button type="submit" class="btn btn-success">{{ __('Save') }}</button></th>
                        @foreach ($roles as $role)
                        <th scope="col" class="text-center">{{ __($role->name) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($permissionGroups as $resource => $permissions)
                    <tr class="table-light">
                        <th colspan="{{ $roles->count() + 1 }}">{{ __(\Illuminate\Support\Str::headline($resource)) }}</th>
                    </tr>
                    @foreach ($permissions as $permissionItem)
                    <tr>
                        <th scope="row" class="ps-4">{{ __(\Illuminate\Support\Str::headline($permissionItem['action'])) }}</th>
                        @foreach ($roles as $role)
                        <td class="text-center">
                            <div class="form-check">
                                <input type="checkbox" name="roles[{{ $role->id }}][{{ $permissionItem['permission']->id }}]"
                                       class="form-check-input"
                                       value="{{ $permissionItem['permission']->id }}"
                                       id="permission-{{ $role->id }}-{{ $permissionItem['permission']->id }}"
                                       @if($role->hasPermissionTo($permissionItem['permission']->name)) checked @endif>
                            </div>
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                    @endforeach
                </tbody>
            </table>
        </form>
    </div>
</div>

@endsection
