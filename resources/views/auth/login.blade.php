@extends('layouts.app')

@section('content')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans&display=swap" rel="stylesheet">
    <style>
        .form-signin {
            max-width: 430px;
            padding: 15px;
            font-family: 'Nunito Sans', sans-serif;
            color: #636b6f;
        }
        .btn-primary {
            background: #2d3748;
        }
        .field-icon {
            float: right;
            margin-top: -30px;
            position: relative;
            z-index: 2;
            margin-left: -150px;
            padding-right: 10px;
        }
    </style>
    <div class="row">
        <div class="form-signin m-auto col-12 col-md-3 h-100">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="text-center">
                        <img src="/asset/img/prospero_flow_crm_logo.svg" alt="{{ env('APP_NAME') }}">
                    </div>
                    <h1 class="h5">{{ __('Login') }}</h1>
                </div>
                <div class="panel-body">
                    <form role="form" method="POST" action="{{ route('login') }}" class="form-horizontal w-100">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }} mb-3">
                            <label for="email" class="control-label mb-2">E-Mail</label>
                            <div>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="form-control form-control-lg @if ($errors->has('email')) is-invalid @endif">
                                <div class="form-control-icon">
                                    <i class="bi bi-person"></i>
                                </div>
                                @if ($errors->has('email'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }} mb-3">
                            <label for="password" class="control-label mb-2">{{ __('Password') }}</label>

                            <div>
                                <input id="password" type="password" name="password" required class="form-control form-control-lg @if ($errors->has('password')) is-invalid @endif">
                                <span toggle="#password" class="las la-eye field-icon toggle-password"></span>
                                @if ($errors->has('password'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group mb-2">
                            <div class="text-center">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> {{ __('Remember me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col mx-auto">
                                <button type="submit" class="w-100 btn btn-primary btn-lg btn-block">{{ __('Login') }}</button>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="text-center">
                                <a class="btn btn-link mt-2" href="{{ route('password.request') }}">{{ __('Forgot your password?') }}</a>
                            </div>
                        </div>
                    </form>

                    <div class="mt-4">
                    <a href="https://roskus.com" target="_blank" rel="noopener">
                        <picture>
                            <source srcset="https://static.roskus.com/img/roskus_powered.webp" type="image/webp">
                            <img src="https://static.roskus.com/img/roskus_powered.png" alt="Powered by Roskus" width="112" height="44">
                        </picture>
                    </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-0 col-md-9 h-100 d-none d-sm-block" style="background: url('/asset/img/bg-auth.jpg'); background-repeat: no-repeat; background-size: cover; min-height: 100vh;">
            <div style="color: #e5e5e5; margin-bottom: 30px; position: absolute; bottom: 0;">Power by roskus and OpenSource</div>
        </div>
    </div>
    @push('scripts')
    <script src="{{ url('/asset/js/Password.js') }}"></script>
    @endpush
@endsection
