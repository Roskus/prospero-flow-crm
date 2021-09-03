@extends('layouts.app')

@section('content')
<header>
   <h1>{{ __('Users') }}</h1>
</header>

<div class="row mb-3">
    <div class="col">
        <a href="/user/add" class="btn btn-primary">{{ __('New') }}</a>
    </div>
</div>

<table class="table table-bordered table-striped">
<thead>
<tr>
    <th>{{ __('Last name') }}</th>
    <th>{{ __('First name') }}</th>
    <th>E-mail</th>
    <th>{{ __('Language') }}</th>
    <th>{{ __('Actions') }}</th>
</tr>
</thead>
<tbody>
@foreach($users as $user)
<tr>
    <td><a href="/user/edit/{{ $user->id}}">{{ $user->last_name }}</a></td>
    <td>{{ $user->first_name }}</td>
    <td>{{ $user->email }}</td>
    <td>{{ $user->lang }}
    <td>
        <a href="/user/edit/{{ $user->id}}" class="btn bt-xs btn-warning"><i class="">E</i></a>
        <a href="/user/delete/{{ $user->id}}" class="btn bt-xs btn-danger"><i class="">D</i></a>
    </td>
</tr>
@endforeach
</tbody>
</table>
@endsection
