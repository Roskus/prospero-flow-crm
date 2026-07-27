@extends('layouts.app')

@section('content')
@include('layouts.partials._header', ['title' => __('Change Password')])

<div class="card">
    <div class="card-body">
        <div class="alert alert-warning">
            <i class="las la-exclamation-triangle"></i>
            {{ __('You are required to change your password before continuing.') }}
        </div>

        <form method="POST" action="{{ route('password.change.update') }}" class="form">
            @csrf

            <div class="row form-group mb-2">
                <div class="col-md-6">
                    <label for="current_password">{{ __('Current password') }} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" name="current_password" id="current_password" required class="form-control">
                        <span class="input-group-text">
                            <i role="button" toggle="#current_password" class="las la-eye toggle-password"></i>
                        </span>
                    </div>
                    @error('current_password')
                        <span class="text-danger"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
            </div>

            <div class="row form-group mb-2">
                <div class="col-md-6">
                    <label for="password">{{ __('New password') }} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" minlength="8" required class="form-control">
                        <span class="input-group-text">
                            <i role="button" toggle="#password" class="las la-eye toggle-password"></i>
                        </span>
                    </div>
                    @error('password')
                        <span class="text-danger"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="password_confirmation">{{ __('Confirm new password') }} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" name="password_confirmation" id="password_confirmation" minlength="8" required class="form-control">
                        <span class="input-group-text">
                            <i role="button" toggle="#password_confirmation" class="las la-eye toggle-password"></i>
                        </span>
                    </div>
                </div>
            </div>

            <div class="row form-group mt-2">
                <div class="col">
                    <button type="submit" class="btn btn-primary">{{ __('Change password') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    $(".toggle-password").click(function() {
        $(this).toggleClass("la-eye la-eye-slash");
        let input = $($(this).attr("toggle"));
        if (input.attr("type") == "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });
</script>
@endpush
@endsection
