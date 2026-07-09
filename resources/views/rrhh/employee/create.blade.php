@extends('layouts.app')

@section('content')
@include('layouts.partials._header', ['title' => __('Create Employee')])

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ url('/rrhh/employee/save') }}" class="form">
            @csrf
            @include('rrhh.employee._form')
            <div class="row mt-2">
                <div class="col">
                    <a href="{{ url('/rrhh') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
