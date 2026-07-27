@extends('layouts.basic')

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
    <div class="row g-0">
        <div class="form-signin m-auto col-12 col-md-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="text-center">
                        <img src="/asset/img/prospero_flow_crm_logo.svg" alt="{{ env('APP_NAME') }}" class="img-fluid">
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
                        @include('powered')
                    </div>
                    <div class="text-muted text-center small mt-1">v{{ APP_VERSION }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-9 d-none d-sm-block" style="background: url('/asset/img/bg-auth.jpg') center/cover no-repeat; min-height: 100vh;"></div>
    </div>
    @push('scripts')
    <script src="{{ url('/asset/js/Password.js') }}"></script>
    @endpush
@endsection
