@extends('layout.app')

@section('content')
<header>
   <h1>{{ __('hammer.Users') }}</h1>
</header>

<table class="table table-bordered">
<thead>
<tr>
    <th>{{ __('Last name') }}</th>
    <th>{{ __('First name') }}</th>
    <th>E-mail</th>
</tr>
</thead>
<tbody>
@foreach($users as $user)
<tr>
    <td><a href="/user/edit/{{ $user->id}}">{{ $user->last_name }}</a></td>
    <td>{{ $user->first_name }}</td>
    <td>{{ $user->email }}</td>
</tr>
@endforeach
</tbody>
</table>
@endsection
