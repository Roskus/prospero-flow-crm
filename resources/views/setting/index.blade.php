@extends('layout.app')

@section('content')
<header>
   <h1>{{ __('hammer.Settings') }}</h1>
</header>

<div class="row">
    <div class="col">
        <a href="/company">{{ __('hammer.Companies') }}</a>
    </div>
</div>
@endsection
