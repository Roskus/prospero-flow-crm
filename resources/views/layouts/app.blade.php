<!doctype html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex">
    <meta name="googlebot" content="noindex">
    <title>{{ env('APP_NAME') }}</title>
    <link rel="icon" href="/favicon.png" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ url('/asset/img/funnel.png') }}">
    <link rel="manifest" href="{{ url('/manifest') }}">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
          integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet"
          href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/js/all.min.js"
            integrity="sha512-2bMhOkE/ACz21dJT8zBOMgMecNxx0d37NND803ExktKiKdSzdwn+L7i9fdccw/3V06gM/DBWKbYmQvKMdAA9Nw=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="/asset/css/prospect-flow.css">
    <link rel="stylesheet" href="/asset/theme/space/css/space.css">
    @auth
        @if(is_file(public_path("/asset/upload/company/".\Illuminate\Support\Str::slug(Auth::user()->company->name, '_')."/".\Illuminate\Support\Str::slug(Auth::user()->company->name, '_').".css")))
        <link rel="stylesheet" href="{{ "/asset/upload/company/".\Illuminate\Support\Str::slug(Auth::user()->company->name, '_')."/".\Illuminate\Support\Str::slug(Auth::user()->company->name, '_').".css" }}">
        @endif
    @endauth
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    @stack('styles')
</head>
<body>
@auth
<header>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top bg-dark d-print-none" data-bs-theme="dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/') }}">
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
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse"
                    aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                @auth
                    @include('menu.top')
                @endauth

                <ul class="navbar-nav navbar-right">
                    @guest
                        @if (Route::has('login'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                        @endif

                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                        @endif
                    @endguest

                    @auth
                        <li class="nav-item dropdown dropstart">
                            <a class="nav-link" href="#" id="notification-list" data-bs-toggle="dropdown" aria-expanded="false" onclick="ProspectFlow.Notification.getLatest()">
                                <i class="fa-regular fa-bell"></i>
                                <span id="notification-badge">
                                    <span class="visually-hidden">{{ __('New notifications') }}</span>
                                </span>
                            </a>
                            <div class="dropdown-menu" style="width: 50vw">
                                <ul class="list-group" id="notification-message-list" aria-labelledby="notification-list">
                                    <li class="p-2">
                                        <div class="m-0 alert alert-warning fade show" role="alert">
                                            {{ __('You have no unread notifications') }}
                                        </div>
                                    </li>
                                </ul>
                                <hr class="dropdown-divider">
                                <a href="{{ url('/notification') }}" class="dropdown-item">{{ __('View notifications') }}</a>
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="dropdown10" data-bs-toggle="dropdown" aria-expanded="false">
                                @if(empty(Auth::user()->photo))
                                    <img src="/asset/img/user.jpg" alt="{{ Auth::user()->first_name }}" width="32" height="32" class="rounded-circle">
                                @else
                                    <img src="/asset/upload/company/{{ \Illuminate\Support\Str::slug(Auth::user()->company->name, '_') }}/{{ Auth::user()->photo }}" alt="{{ Auth::user()->first_name }}" width="32" height="32" class="rounded-circle">
                                @endif
                                {{ Auth::user()->first_name }}
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="dropdown10">
                                <li>
                                    <a href="{{ url('/profile') }}" class="dropdown-item">
                                        <i class="las la-user-tie"></i> {{ __('Profile') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('/setting') }}" class="dropdown-item">
                                        <i class="las la-cogs"></i> {{ __('Setting') }}
                                    </a>
                                </li>
                                <li role="separator" class="dropdown-divider"></li>
                                <li>
                                    <a href="#" onclick="Hammer.exit('{{ __('Do you want to exit?') }}')" class="dropdown-item">
                                        <i class="las la-door-open"></i> {{ __('Exit') }}</a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>
</header>
@endauth
<main>
    @auth
        @include('menu.sidebar')
    @endauth
    <div class="container-fluid @auth mt-5 pt-3 @endauth" style="overflow-y: scroll;">
        @include('layouts.partials._errors')
        @yield('content')
        <footer class="d-print-none">
            <div class="text-center">
                <small class="">{{ env('APP_NAME') }} | Ver. {{ config('app.version') }}</small>
            </div>
        </footer>
    </div>
</main>

<div id="notifications-toast-container" class="toast-container position-fixed bottom-0 end-0 p-3"></div>

<!--JavaScript-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
   integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
<script src="/asset/js/ProspectFlow.js"></script>
<script src="/asset/js/Notification.js"></script>
@stack('scripts')
</body>
</html>
