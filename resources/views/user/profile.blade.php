@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', ['title' => __('Profile')])

    @if(session('status'))
        <div class="alert alert-{{ session('status') }} mt-2">
            {!! __(session('message'))  !!}
        </div>
    @endif
    <div class="card">
        <div class="card-body">
        <form method="POST" action="{{ url('/profile/save') }}" enctype="multipart/form-data" class="form">
            @csrf

            <div class="row form-group{{ $errors->has('name') ? ' has-error' : '' }} mb-2">
                <div class="col">
                    <label for="first_name" class="col-md-4 control-label">{{ __('Name') }} <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" id="first_name" value="{{ @old('first_name', $user->first_name) }}" required autofocus class="form-control form-control-lg">

                    @if ($errors->has('name'))
                        <span class="help-block">
                            <strong>{{ $errors->first('first_name') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="col">
                    <label for="last_name" class="col-md-4 control-label">{{ __('Last name') }}</label>
                    <input type="text" name="last_name" id="last_name" value="{{ $user->last_name }}" required class="form-control form-control-lg">

                    @if ($errors->has('last_name'))
                        <span class="help-block">
                            <strong>{{ $errors->first('last_name') }}</strong>
                        </span>
                    @endif
                </div>
            </div><!--./row-->

            <div class="row form-group mb-2">
                <div class="col {{ $errors->has('email') ? ' has-error' : '' }}">
                    <label for="email" class="col-md-4 control-label">E-Mail <span class="text-danger">*</span></label>
                    <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required class="form-control form-control-lg">

                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div><!--./col-->
            </div><!--./row-->

            <div class="row">
                <div class="col">
                    <label for="lang" class="col-md-4 control-label">{{ __('Language') }}</label>
                    <select name="lang" id="lang" required="required" class="form-control form-control-lg">
                        <option value=""></option>
                        @foreach ($languages as $code => $name)
                            <option value="{{ $code }}" @if(Auth::user()->lang == $code) selected="selected" @endif>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col">
                    <label for="timezone" class="control-label">{{ __('Timezones') }}</label>
                    <input name="timezone" id="timezone" list="timezoneOptions" value="{{ $user->timezone }}" placeholder="{{ __('Type to search...') }}" autocomplete="off"  class="form-control form-control-lg">

                    <datalist id="timezoneOptions">
                        @foreach ($timezones as $name)
                            <option value="{{ $name }}">
                        @endforeach
                    </datalist>
                </div>
            </div>

            <div class="row form-group{{ $errors->has('password') ? ' has-error' : '' }} mb-2">
                <div class="col">
                    <label for="password" class="col-md-4 control-label">{{ __('Password') }}</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" autocomplete="off" class="form-control form-control-lg">
                        <span class="input-group-text"><i role="button" toggle="#password" class="las la-eye toggle-password"></i></span>
                    </div>

                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="col-md-6">
                    <label for="password-confirm" class="col-md-4 control-label">{{ __('Confirm password') }}</label>
                    <div class="input-group">
                        <input type="password" name="password_confirmation" id="password-confirm" autocomplete="off" class="form-control form-control-lg">
                        <span class="input-group-text"><i role="button" toggle="#password-confirm" class="las la-eye toggle-password"></i></span>
                    </div>
                </div>
            </div><!--./row-->

            <div class="row form-group mb-2">
                <div class="col">
                    <label for="phone" class="col-md-4 control-label">{{ __('Phone') }}</label>
                    <input type="tel" name="phone" id="phone" value="{{ $user->phone }}" maxlength="15" class="form-control form-control-lg">
                </div>
                <div class="col">
                    <label for="photo">{{ __('Photo') }}</label>
                    <input type="file" name="photo" id="photo" accept="image/png, image/gif, image/jpeg" class="form-control form-control-lg">
                    @if($user->photo)
                        <img src="/asset/upload/company/{{ \Illuminate\Support\Str::slug($user->company->name, '_') }}/{{ $user->photo }}" height="128" alt="" class="mt-1">
                    @endif
                </div>
            </div>
            <div class="row form-group mb-2">
                <div class="col">
                    <label for="signature_html">{{ __('Signature') }}</label>
                    <textarea name="signature_html" id="signature_html">{!! (!empty($user->signature_html)) ? $user->signature_html : old('signature_html','') !!}</textarea>
                </div>
            </div>
            <div class="row form-group mt-2">
                <div class="col-md-6 col-md-offset-4">
                    <a href="{{ url('/') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                </div>
            </div>
        </form>
        </div><!--./card-body-->
    </div><!--./card-->
    @push('scripts')
    <script>
        $(".toggle-password").click(function() {
            $(this).toggleClass("la-eye la-eye-slash");
            var input = $($(this).attr("toggle"));
            if (input.attr("type") == "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });
    </script>
    @include('html_editor', ['id' => 'signature_html', 'placeholder' => 'Email signature'])
    @endpush
@endsection
