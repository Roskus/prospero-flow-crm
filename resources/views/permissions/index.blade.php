@extends('layouts.app')

@section('content')
@include('layouts.partials._header', ['title' => __('Permissions')])

<div class="card">
    <div class="card-body">
        <form action="{{ url('/permission') }}" method="POST">
            @csrf
            <table class="table">
                <thead>
                    <tr>
                        <th><button type="submit" class="btn btn-success">{{ __('Save') }}</button></th>
                        @foreach ($roles as $role)
                        <th scope="col">{{ $role->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($permissions as $permission)
                    <tr>
                        <th scope="row">{{ $permission->name }}</th>
                        @foreach ($roles as $role)
                        <th scope="col">
                            <div class="form-check">
                                <input name="roles[{{ $role->id }}][{{ $permission->id }}]" class="form-check-input" type="checkbox" value="{{ $permission->id }}" id="flexCheckChecked" @if($role->hasPermissionTo($permission->name)) checked @endif>
                            </div>
                        </th>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </form>
    </div>
</div>

@endsection
