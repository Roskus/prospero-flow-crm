@extends('layouts.app')

@section('content')

    <div class="row align-items-center">
        <div class="col">
            <div class="panel panel-default mt-5">
                <div class="panel-heading">
                    <h1>{{ __('Login') }}</h1>
                </div>
                <div class="panel-body">
                    <form role="form" method="POST" action="{{ route('login') }}" class="form-horizontal mt-5">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }} mb-3">
                            <label for="email" class="col-md-4 control-label mb-2">E-Mail</label>

                            <div class="col-md-6">
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="form-control form-control-lg">
                                <div class="form-control-icon">
                                    <i class="bi bi-person"></i>
                                </div>
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }} mb-3">
                            <label for="password" class="col-md-4 control-label mb-2">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" name="password" required class="form-control form-control-lg">

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col mx-auto">
                                <button type="submit" class="btn btn-primary btn-lg btn-block">{{ __('Login') }}</button>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col">
                                <a class="btn btn-link" href="{{ route('password.request') }}">{{ __('Forgot Your Password?') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
