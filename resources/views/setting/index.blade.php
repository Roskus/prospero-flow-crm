@extends('layouts.app')

@section('content')
<header>
   <h1>{{ __('Settings') }}</h1>
</header>

<div class="row">
    @can('read company')
    <div class="col text-center mb-1">
        <div class="card">
            <div class="card-body">
                <a href="{{ url('/company') }}">
                    <div class="h1">
                        <i class="las la-industry"></i>
                    </div>
                    {{ __('Companies') }}
                </a>
            </div>
        </div>
    </div>
    @endcan

    <div class="col text-center mb-1">
        <div class="card">
            <div class="card-body">
                <a href="{{ url('/category') }}">
                    <div class="h1">
                        <i class="las la-archive"></i>
                    </div>
                    {{ __('Categories') }}
                </a>
            </div>
        </div>
    </div>

    <div class="col text-center mb-1">
        <div class="card">
            <div class="card-body">
                <a href="{{ url('/brand') }}">
                    <div class="h1">
                        <i class="las la-apple-alt"></i>
                    </div>
                    {{ __('Brands') }}
                </a>
            </div>
        </div>
    </div>

    @can('read user')
    <div class="col text-center mb-1">
        <div class="card">
            <div class="card-body">
                <a href="{{ url('/user') }}">
                    <div class="h1">
                        <i class="las la-users"></i>
                    </div>
                    {{ __('Users') }}
                </a>
            </div>
        </div>
    </div>
    @endcan

    <div class="col text-center mb-1">
        <div class="card">
            <div class="card-body">
                <a href="{{ url('/currency') }}">
                    <div class="h1">
                        <i class="las la-euro-sign"></i>
                    </div>
                    {{ __('Currencies') }}
                </a>
            </div>
        </div>
    </div>

    <div class="col text-center mb-1">
        <div class="card">
            <div class="card-body">
                <a href="{{ url('/email-template') }}">
                    <div class="h1">
                        <i class="las la-envelope-open-text"></i>
                    </div>
                    {{ __('Email templates') }}
                </a>
            </div>
        </div>
    </div>

    @role('SuperAdmin')
    <div class="col text-center mb-1">
        <div class="card">
            <div class="card-body">
                <a href="{{ url('/permission') }}">
                    <div class="h1">
                        <i class="las la-lock-open"></i>
                    </div>
                    {{ __('Permissions') }}
                </a>
            </div>
        </div>
    </div>
    @endrole

    @role('SuperAdmin')
    <div class="col text-center mb-1">
        <div class="card">
            <div class="card-body">
                <a href="{{ url('/bank') }}">
                    <div class="h1">
                        <i class="las la-university"></i>
                    </div>
                    {{ __('Banks') }}
                </a>
            </div>
        </div>
    </div>
    @endrole
</div>
@endsection
