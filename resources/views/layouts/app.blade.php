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
    <link rel="apple-touch-icon" href="{{ url('/asset/img/hammer.png') }}">
    <link rel="manifest" href="{{ url('/manifest') }}">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
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
</head>
<body>
    {{-- ICONS DARK THEME TOGGLE --}}
    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
        <symbol id="moon-stars-fill" viewBox="0 0 16 16">
            <path
                d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278z" />
            <path
                d="M10.794 3.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387a1.734 1.734 0 0 0-1.097 1.097l-.387 1.162a.217.217 0 0 1-.412 0l-.387-1.162A1.734 1.734 0 0 0 9.31 6.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387a1.734 1.734 0 0 0 1.097-1.097l.387-1.162zM13.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.156 1.156 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.156 1.156 0 0 0-.732-.732l-.774-.258a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732L13.863.1z" />
        </symbol>
        <symbol id="sun-fill" viewBox="0 0 16 16">
            <path
                d="M8 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z" />
        </symbol>
        <symbol id="check2" viewBox="0 0 16 16">
            <path
                d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z" />
        </symbol>
        <symbol id="circle-half" viewBox="0 0 16 16">
            <path d="M8 15A7 7 0 1 0 8 1v14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16z" />
        </symbol>        
    </svg>
@auth
<header>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top bg-dark" data-bs-theme="dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/') }}">
                @if(empty(Auth::user()->company->logo))
                    {{ env('APP_NAME') }}
                @else
                   <img src="/asset/upload/company/{{ \Illuminate\Support\Str::slug(Auth::user()->company->name, '_') }}/{{ Auth::user()->company->logo }}" alt="{{ env('APP_NAME') }}" class="logo">
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
                    {{-- DARK THEME TOGGLE --}}
                    <li class="nav-item dropdown">
                        <button class="btn btn-link nav-link py-2 px-0 px-lg-2 dropdown-toggle d-flex align-items-center"
                                id="bd-theme"
                                type="button"
                                aria-expanded="false"
                                data-bs-toggle="dropdown"
                                data-bs-display="static">
                            <svg class="bi my-1 theme-icon-active"><use href="#circle-half"></use></svg>
                            <span class="d-lg-none ms-2">Toggle theme</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="bd-theme" style="--bs-dropdown-min-width: 8rem;">
                            <li>
                            <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light">
                                <svg class="bi me-2 opacity-50 theme-icon"><use href="#sun-fill"></use></svg>
                                Light
                                <svg class="bi ms-auto d-none"><use href="#check2"></use></svg>
                            </button>
                            </li>
                            <li>
                            <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark">
                                <svg class="bi me-2 opacity-50 theme-icon"><use href="#moon-stars-fill"></use></svg>
                                Dark
                                <svg class="bi ms-auto d-none"><use href="#check2"></use></svg>
                            </button>
                            </li>
                            <li>
                            <button type="button" class="dropdown-item d-flex align-items-center active" data-bs-theme-value="auto">
                                <svg class="bi me-2 opacity-50 theme-icon"><use href="#circle-half"></use></svg>
                                Auto
                                <svg class="bi ms-auto d-none"><use href="#check2"></use></svg>
                            </button>
                            </li>
                        </ul>
                    </li>
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
                        <li class="nav-item dropdown">
                            <a class="nav-link" href="#" id="notification-list" data-bs-toggle="dropdown" aria-expanded="false" onclick="ProspectFlow.Notification.getLatest()">
                                <i class="fa-regular fa-bell"></i>
                                <span class="notification-badge animation-blink">
                                    <span class="visually-hidden">{{ __('New notifications') }}</span>
                                </span>
                            </a>
                            <ul id="notification-message-list" class="dropdown-menu" aria-labelledby="notification-list"></ul>
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
    </div>
</main>
<!--JavaScript-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
<script src="/asset/js/Hammer.js"></script>
<script src="/asset/js/Notification.js"></script>
<script src="{{ asset('asset/js/dark-theme.js') }}"></script>
@stack('scripts')
</body>
</html>
