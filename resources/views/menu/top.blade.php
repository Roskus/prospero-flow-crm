<ul class="navbar-nav me-auto mb-2 mb-md-0">
    @can('read dashboard')
    <li class="nav-item active">
        <a href="{{ url('/') }}" class="nav-link fs-6 @if(Request::path() == '/') active @endif">
            <i class="las la-compass"></i> {{ __('Dashboard') }}
        </a>
    </li>
    @endcan
    @can('read lead')
    <li class="nav-item">
        <a href="{{ url('/lead') }}" class="nav-link fs-6 @if(Request::path() == 'lead') active @endif">
            <i class="las la-filter"></i> {{ __('Leads') }}
        </a>
    </li>
    @endcan
    @can('read customer')
    <li class="nav-item">
        <a href="{{ url('/customer') }}" class="nav-link fs-6 @if(Request::path() == 'customer') active @endif">
            <i class="las la-user-alt"></i> {{ __('Customers') }}
        </a>
    </li>
    @endcan
    @can('read order')
    <li>
        <a href="{{ url('/order') }}" class="nav-link fs-6 @if(Request::path() == 'order') active @endif">
            <i class="las la-receipt"></i> {{ __('Orders') }}
        </a>
    </li>
    @endcan
    @can('read product')
    <li>
        <a href="{{ url('/product') }}" class="nav-link fs-6 @if(Request::path() == 'product') active @endif">
            <i class="las la-box"></i> {{ __('Products') }}
        </a>
    </li>
    @endcan
    @canany(['read email', 'create campaign'])
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle fs-6" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="las la-envelope-open-text"></i> E-mail
        </a>
        <ul class="dropdown-menu">
            @can('read email')
            <li>
                <a href="{{ url('/email') }}" class="dropdown-item @if(Request::path() == 'email') active @endif fs-6">
                    <i class="las la-envelope-open-text"></i> E-mail
                </a>
            </li>
            @endcan
            @can('create campaign')
            <li>
                <a href="{{ url('/campaign') }}" class="dropdown-item @if(Request::path() == 'campaign') active @endif fs-6">
                    <i class="las la-mail-bulk"></i> {{ __('Campaign') }}
                </a>
            </li>
            @endcan
        </ul>
    </li>
    @endcanany
    @canany(['read supplier', 'read calendar', 'read accounting', 'read ticket'])
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle fs-6" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="las la-plus"></i> {{ __('More') }}
        </a>
        <ul class="dropdown-menu">
            @can('read supplier')
            <li>
                <a href="{{ url('/supplier') }}" class="dropdown-item fs-6 @if(Request::path() == 'supplier') active @endif" title="{{ __('Suppliers') }}">
                    <i class="las la-dolly"></i> {{ __('Suppliers') }}
                </a>
            </li>
            @endcan
            @can('read calendar')
            <li>
                <a href="{{ url('/calendar') }}" class="dropdown-item fs-6 @if(Request::path() == 'calendar') active @endif">
                    <i class="las la-calendar"></i> {{ __('Calendar') }}
                </a>
            </li>
            @endcan
            @can('read accounting')
            <li class="nav-item fs-6">
                <a href="{{ url('/accounting') }}" class="dropdown-item @if(Request::path() == 'accounting') active @endif">
                    <i class="las la-coins"></i> {{ __('Accounting') }}
                </a>
            </li>
            @endcan
            @can('read ticket')
            <li>
                <a href="{{ url('/ticket') }}" class="dropdown-item fs-6 @if(Request::path() == 'ticket') active @endif" title="{{ __('Tickets') }}">
                    <i class="las la-tools"></i> {{ __('Tickets') }}
                </a>
            </li>
            @endcan
        </ul>
    </li>
    @endcanany
</ul>
