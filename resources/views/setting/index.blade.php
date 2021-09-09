@extends('layouts.app')

@section('content')
<header>
   <h1>{{ __('Settings') }}</h1>
</header>

<div class="row">
    <div class="col">
        <a href="/company">
            <i class="material-icons">business</i>
            {{ __('Companies') }}
        </a>
    </div>
    <div class="col">
        <a href="/category">
            <i class="material-icons">folder</i>
            {{ __('Categories') }}
        </a>
    </div>
    <div class="col">
        <a href="/brand">
            <i class="material-icons">folder</i>
            {{ __('Brands') }}
        </a>
    </div>
    <div class="col">
        <a href="/user">
            <i class="material-icons">
            supervisor_account
            </i>
            {{ __('Users') }}
        </a>
    </div>
    <div class="col">
        <a href="/currency">
            <i class="las la-money-bill"></i>
            {{ __('Currencies') }}
        </a>
    </div>
</div>
@endsection
