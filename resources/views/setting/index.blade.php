@extends('layouts.app')

@section('content')
<header>
   <h1>{{ __('Settings') }}</h1>
</header>

<div class="row">
    <div class="col">
        <a href="/company">
            <div class="h1">
                <i class="material-icons">business</i>
            </div>
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
            <div class="h1">
                <i class="las la-apple-alt"></i>
            </div>
            {{ __('Brands') }}
        </a>
    </div>
    <div class="col">
        <a href="/user">
            <div class="h1">
                <i class="las la-users"></i>
            </div>
            {{ __('Users') }}
        </a>
    </div>
    <div class="col">
        <a href="/currency">
            <div class="h1">
                <i class="las la-euro-sign"></i>
            </div>
            {{ __('Currencies') }}
        </a>
    </div>
</div>
@endsection
