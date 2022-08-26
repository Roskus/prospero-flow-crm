@extends('layouts.app')

@section('content')
    <header>
        <h1>{{ __('User') }}</h1>
    </header>
    <form method="POST" action="/user/save">
        @csrf

        <div class="row form-group mb-3">
            <div class="col">
                <label for="name" class="control-label">{{ __('First name') }}</label>
                <input type="text" name="first_name" id="first_name" value="{{ $user->first_name }}" required autofocus class="form-control form-control-lg">
            </div>
            <div class="col">
                <label for="name" class="control-label">{{ __('Last name') }}</label>
                <input type="text" name="last_name" id="last_name" value="{{ $user->last_name }}" required class="form-control form-control-lg">
            </div>
        </div>

        <div class="row form-group mb-3">
            <div class="col">
                <label for="email" class="col-md-4 control-label">E-Mail</label>
                <input type="email" name="email" id="email" value="{{ $user->email }}" required class="form-control form-control-lg">
            </div>
            <div class="col">
                <label for="lang" class="col-md-4 control-label">{{ __('Language') }}</label>
                <select name="lang" id="lang" required="required" class="form-control form-control-lg">
                    <option value=""></option>
                    @foreach ($languages as $code => $name)
                    <option value="{{ $code }}" @if($user->lang == $code) selected="selected" @endif>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row form-group mb-3">
            <div class="col {{ $errors->has('password') ? ' has-error' : '' }}">
                <label for="password" class="control-label">{{ __('Password') }}</label>
                <input id="password" type="password" name="password" autocomplete="off"  class="form-control form-control-lg">
                @if ($errors->has('password'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
            </div><!--./col-->
            <div class="col">
                <label for="password-confirm" class="control-label">{{ __('Confirm password') }}</label>
                <input type="password" name="password_confirmation" id="password-confirm" autocomplete="off" class="form-control form-control-lg">
            </div>
        </div>

        <div class="row form-group">
            <div class="col text-end">
                <a href="/user" class="btn btn-secondary btn-lg">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary btn-lg">{{__('Save')}}</button>
            </div>
        </div>
        <input type="hidden" name="id" value="{{ ($user->id) ?? $user->id }}">
    </form>
@endsection
