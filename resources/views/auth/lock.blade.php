@extends('layouts.basic')

@section('content')
    <style>
        .lockscreen-wrapper {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background-color: #f7f7f7;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .lockscreen-logo {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: bold;
            margin-right: 20px;
        }

        .lockscreen-name {
            text-align: center;
            margin-bottom: 20px;
        }

        .lockscreen-item {
            width: 300px; /* Puedes ajustar este valor según tus necesidades */
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .field-icon {
            float: right;
            position: relative;
            z-index: 2;
            padding-right: 10px;
            left: -26px;
            padding-top: 10px;
        }
    </style>
    <div id="lockscreen" class="lockscreen-wrapper">
    <div class="lockscreen-logo">
        <div>
        @if(empty(Auth::user()->company->logo))
            {{ env('APP_NAME') }}
        @else
            <img src="/asset/upload/company/{{ \Illuminate\Support\Str::slug(Auth::user()->company->name, '_') }}/{{ Auth::user()->company->logo }}" alt="{{ env('APP_NAME') }}" class="logo">
        @endif
        @if(!App::environment('production'))
            <span class="float-right">
                <small class="">{{ env('APP_ENV') }}</small>
            </span>
        @endif
        </div>
        <b>Bienvenido, {{ Auth::user()->first_name }}</b>
    </div>

    <div class="lockscreen-item">
        <div class="lockscreen-name">
            <div>
                @if(empty(Auth::user()->photo))
                    <img src="/asset/img/user.jpg" alt="{{ Auth::user()->first_name }}" width="64" height="64" class="rounded-circle">
                @else
                    <img src="/asset/upload/company/{{ \Illuminate\Support\Str::slug(Auth::user()->company->name, '_') }}/{{ Auth::user()->photo }}" alt="{{ Auth::user()->first_name }}" width="64" height="64" class="rounded-circle">
                @endif
            </div>
            {{ Auth::user()->first_name }}
        </div>
        <form action="{{ route('unlock') }}" method="post" autocomplete="false">
            @csrf
            <div class="input-group">
                <input type="password" name="password" id="password" placeholder="{{ __('Password') }}" required
                       autocomplete="new-password" class="form-control @if ($errors->has('password')) is-invalid @endif">
                <span toggle="#password" class="las la-eye field-icon toggle-password"></span>
                <div class="input-group-append">
                    <button type="submit" class="btn"><i class="fa fa-arrow-right text-muted"></i></button>
                </div>
            </div>
            @if ($errors->has('password'))
                <div class="invalid-feedback">
                    <strong>{{ $errors->first('password') }}</strong>
                </div>
            @endif
        </form>
    </div>
</div>
@push('scripts')
    <script src="{{ url('/asset/js/Password.js') }}"></script>
@endpush
@endsection
