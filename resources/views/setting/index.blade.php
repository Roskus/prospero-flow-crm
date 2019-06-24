@extends('layout.app')

@section('content')
<header>
   <h1>{{ __('hammer.Settings') }}</h1>
</header>

<div class="row">
    <div class="col">
        <a href="/company">
            <i class="material-icons">business</i>
            {{ __('hammer.Companies') }}
        </a>
    </div>
    <div>
        <a href="/category">
            <i class="material-icons">folder</i>
            {{ __('hammer.Categories') }}
        </a>
    </div>
    <div class="col">
        <a href="/user">
            <i class="material-icons">
            supervisor_account
            </i>
            {{ __('hammer.Users') }}
        </a>
    </div>
</div>
@endsection
