@extends('layouts.app')

@section('content')
    <header>
        <h1>{{ __('User') }}</h1>
    </header>
    <form method="POST" action="/user/save" class="form">
        @csrf

        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
            <label for="name" class="col-md-4 control-label">{{ __('Name') }}</label>

            <div class="col-md-6">
                <input type="text" name="first_name" id="first_name" value="{{ $user->first_name }}" required autofocus class="form-control">

                @if ($errors->has('name'))
                    <span class="help-block">
                        <strong>{{ $errors->first('name') }}</strong>
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
                    <option value="{{ $code }}" @if($user->lang == $code) selected="selected" @endif>{{ $name }}</option>
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

        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
            <label for="password" class="col-md-4 control-label">{{ __('Password') }}</label>

            <div class="col-md-6">
                <input id="password" type="password" class="form-control" name="password" autocomplete="off">

                @if ($errors->has('password'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group">
            <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>

            <div class="col-md-6">
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" autocomplete="off">
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-6 col-md-offset-4">
                <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
            </div>
        </div>
        <input type="hidden" name="id" value="{{ ($user->id) ?? $user->id }}">
    </form>
@endsection
