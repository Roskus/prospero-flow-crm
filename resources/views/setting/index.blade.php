@extends('layouts.app')

@section('content')
<header>
   <h1>{{ __('Settings') }}</h1>
</header>

<div class="row">
    @can('read company')
    <div class="col text-center">
        <a href="{{ url('/company') }}">
            <div class="h1">
                <i class="las la-industry"></i>
            </div>
            {{ __('Companies') }}
        </a>
    </div>
    @endcan

    <div class="col text-center">
        <a href="{{ url('/category') }}">
            <div class="h1">
                <i class="las la-archive"></i>
            </div>
            {{ __('Categories') }}
        </a>
    </div>

    <div class="col text-center">
        <a href="{{ url('/brand') }}">
            <div class="h1">
                <i class="las la-apple-alt"></i>
            </div>
            {{ __('Brands') }}
        </a>
    </div>

    @can('read user')
    <div class="col text-center">
        <a href="{{ url('/user') }}">
            <div class="h1">
                <i class="las la-users"></i>
            </div>
            {{ __('Users') }}
        </a>
    </div>
    @endcan


    <div class="col text-center">
        <a href="{{ url('/currency') }}">
            <div class="h1">
                <i class="las la-euro-sign"></i>
            </div>
            {{ __('Currencies') }}
        </a>
    </div>

        <div class="col text-center">
            <a href="{{ url('/email-template') }}">
                <div class="h1">
                    <i class="las la-envelope-open-text"></i>
                </div>
                {{ __('Email templates') }}
            </a>
        </div>
</div>
@endsection
