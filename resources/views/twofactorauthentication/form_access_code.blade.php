@extends('layouts.app')

@section('content')

<div class="m-5 p-5">
    <form action="/two-factor-authentication/verify" method="POST">
        @csrf

        <input name="one_time_password" type="text">

        <button type="submit">Authenticate</button>
    </form>
</div>

@endsection
