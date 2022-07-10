@extends('layouts.app')

@section('content')
    <header>
        <h1>{{ trans('hammer.Profile') }}</h1>
    </header>
    <form method="POST" action="/profile/save" class="form">
        @csrf

        <div class="row form-group{{ $errors->has('name') ? ' has-error' : '' }}">
            <div class="col">
                <label for="name" class="col-md-4 control-label">{{ __('Name') }}</label>
                <input type="text" name="first_name" id="first_name" value="{{ $user->first_name }}" required autofocus class="form-control">

                @if ($errors->has('name'))
                    <span class="help-block">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                @endif
            </div>
            <div class="col">
                <label for="last_name" class="col-md-4 control-label">{{ __('Last Name') }}</label>
                <input type="text" name="last_name" id="last_name" value="{{ $user->last_name }}" required autofocus class="form-control">

                @if ($errors->has('last_name'))
                    <span class="help-block">
                        <strong>{{ $errors->first('last_name') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group">
            <div class="col">
                <label for="lang" class="col-md-4 control-label">{{ __('Language') }}</label>
                <select name="lang" id="lang" required="required" class="form-control">
                    <option value=""></option>
                    @foreach ($languages as $code => $name)
                    <option value="{{ $code }}" @if(Auth::user()->lang == $code) selected="selected" @endif>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
            <label for="email" class="col-md-4 control-label">E-Mail</label>

            <div class="col-md-6">
                <input id="email" type="email" class="form-control" name="email" value="{{ $user->email }}" required>

                @if ($errors->has('email'))
                    <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="row form-group{{ $errors->has('password') ? ' has-error' : '' }}">
            <div class="col">
                <label for="password" class="col-md-4 control-label">{{ __('Password') }}</label>
                <input id="password" type="password" class="form-control" name="password" autocomplete="off">

                @if ($errors->has('password'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
            </div>

            <div class="col-md-6">
                <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" autocomplete="off">
            </div>
        </div>
        <div class="row form-group mt-2">
            <div class="col-md-6 col-md-offset-4">
                <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
            </div>
        </div>
    </form>
@endsection
