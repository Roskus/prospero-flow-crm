<ul class="navbar-nav me-auto mb-2 mb-md-0">
    <li class="nav-item active">
        <a href="{{ url('/') }}" class="nav-link fs-5 @if(Request::path() == '/') active @endif">
            <i class="las la-compass"></i> {{ __('Dashboard') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ url('/lead') }}" class="nav-link fs-5 @if(Request::path() == 'lead') active @endif">
            <i class="las la-filter"></i> {{ __('Leads') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ url('/customer') }}" class="nav-link fs-5 @if(Request::path() == 'customer') active @endif">
            <i class="las la-user-alt"></i> {{ __('Customers') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ url('/order') }}" class="nav-link fs-5 @if(Request::path() == 'order') active @endif">
            <i class="las la-receipt"></i> {{ __('Orders') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ url('/product') }}" class="nav-link fs-5 @if(Request::path() == 'product') active @endif">
            <i class="las la-box"></i> {{ __('Products') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ url('/calendar') }}" class="nav-link fs-5 @if(Request::path() == 'calendar') active @endif">
            <i class="las la-calendar"></i> {{ __('Calendar') }}
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ url('/email') }}" class="nav-link @if(Request::path() == 'email') active @endif fs-5">
            <i class="las la-envelope-open-text"></i> E-mail
        </a>
    </li>
    <li class="nav-item fs-5">
        <a href="{{ url('/accounting') }}" class="nav-link @if(Request::path() == 'accounting') active @endif">
            <i class="las la-coins"></i> {{ __('Accounting') }}
        </a>
    </li>
    <li class="nav-item fs-5">
        <a href="{{ url('/supplier') }}" class="nav-link @if(Request::path() == 'supplier') active @endif" title="{{ __('Suppliers') }}">
            <i class="las la-dolly"></i> {{ __('Suppliers') }}
        </a>
    </li>
</ul>
