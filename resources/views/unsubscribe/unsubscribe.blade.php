@extends('layouts.app')

@section('content')
    <div class="container">
        <header>
            <h1>{{ __('Unsubscribe') }}</h1>
        </header>
        <form method="post" action="{{ url('unsubscribe/save') }}">
            @if(session('message'))
                <div class="row">
                    <div class="col">
                        <div class="alert alert-success mt-2">
                            {{ __(session('message')) }}
                        </div>
                    </div>
                </div>
            @endif

            @csrf
            <div class="row">
                <div class="col">
                    <p>{{ __('If you want to stop receiving notifications enter your email') }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="email">E-mail</label>
                    <input type="email" name="email" required="required" class="form-control form-control-lg">
                </div>
            </div>
            <div class="row mt-2">
                <div class="col">
                    <button type="submit" class="btn btn-primary">{{ __('Send') }}</button>
                </div>
            </div>
        </form>
    </div>
@endsection
