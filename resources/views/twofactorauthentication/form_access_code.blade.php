@extends('layouts.app')

@section('content')

<div class="mt-2 container h-75">
    <div class="row align-items-center h-100">
        <div class="col-6 offset-3">
            <div class="card">
            <div class="card-body">
                <p>
                    {{ __('Please confirm access to your account by entering the authentication code provided by your authenticator application.') }}
                </p>
                <form action="/two-factor-authentication/verify" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="one_time_password">{{ __('Code') }}</label>
                        <input class="form-control" id="one_time_password" name="one_time_password" type="number">
                    </div>
                    <div class="text-end">
                        <a class="text-dark me-3" href="#">{{ __('Use a recovery code') }}</a>
                        <button type="submit" class="btn btn-primary">{{ __('Authenticate') }}</button>
                    </div>                    
                </form>
            </div>
        </div>
        </div>
        
    </div>
    
</div>

@endsection
