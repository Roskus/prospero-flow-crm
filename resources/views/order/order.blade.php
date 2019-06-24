@extends('layout.app')

@section('content')
<header>
   <h1>{{ trans('hammer.Order') }}</h1>
</header>

<form method="post" action="/order/save">

    {{ @csrf }}
</form>
@endsection
