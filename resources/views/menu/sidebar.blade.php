<div class="d-flex flex-column flex-shrink-0 bg-light" style="width: 4.5rem;">
    <a href="/" class="d-block p-3 link-dark text-decoration-none" title="Icon-only" data-bs-toggle="tooltip" data-bs-placement="right">
        <svg class="bi" width="40" height="32"><use xlink:href="#bootstrap"/></svg>
        <span class="visually-hidden">Icon-only</span>
    </a>

    <ul class="nav nav-pills nav-flush flex-column mb-auto text-center">
        <li class="nav-item">
            <a href="{{ url('/') }}" class="nav-link py-3 border-bottom @if(Request::path() == '/') active @endif" aria-current="page" title="Dashoard" data-bs-toggle="tooltip" data-bs-placement="right">
                <i class="las la-compass fs-1"></i>
            </a>
        </li>
        <li>
            <a href="{{ url('/lead') }}" class="nav-link py-3 border-bottom @if(Request::path() == 'lead') active @endif" title="{{ __('Leads') }}" data-bs-toggle="tooltip" data-bs-placement="right">
                <i class="las la-filter fs-1"></i>
            </a>
        </li>
        <li>
            <a href="{{ url('/customer') }}" class="nav-link py-3 border-bottom @if(Request::path() == 'customer') active @endif" title="{{ __('Customers') }}" data-bs-toggle="tooltip" data-bs-placement="right">
                <i class="las la-user-alt fs-1"></i>
            </a>
        </li>
        <li>
            <a href="{{ url('/order') }}" class="nav-link py-3 border-bottom @if(Request::path() == 'order') active @endif" title="{{ __('Orders') }}" data-bs-toggle="tooltip" data-bs-placement="right">
                <i class="las la-receipt fs-1"></i>
            </a>
        </li>
        <li>
            <a href="{{ url('/product') }}" class="nav-link py-3 border-bottom @if(Request::path() == 'product') active @endif" title="{{ __('Products') }}" data-bs-toggle="tooltip" data-bs-placement="right">
                <i class="las la-box fs-1"></i>
            </a>
        </li>
        <li>
            <a href="{{ url('/calendar') }}" class="nav-link py-3 border-bottom @if(Request::path() == 'calendar') active @endif" title="{{ __('Calendar') }}" data-bs-toggle="tooltip" data-bs-placement="right">
                <i class="las la-calendar fs-1"></i>
            </a>
        </li>
        <li>
            <a href="{{ url('/email') }}" class="nav-link py-3 border-bottom @if(Request::path() == 'email') active @endif" title="E-mail" data-bs-toggle="tooltip" data-bs-placement="right">
                <i class="las la-envelope-open-text fs-1"></i>
            </a>
        </li>
        <li>
            <a href="{{ url('/accounting') }}" class="nav-link py-3 border-bottom @if(Request::path() == 'accounting') active @endif" title="{{ __('Accounting') }}" data-bs-toggle="tooltip" data-bs-placement="right">
                <i class="las la-coins fs-1"></i>
            </a>
        </li>
        <li>
            <a href="{{ url('/supplier') }}" class="nav-link py-3 border-bottom @if(Request::path() == 'supplier') active @endif" title="{{ __('Suppliers') }}" data-bs-toggle="tooltip" data-bs-placement="right">
                <i class="las la-dolly fs-1"></i>
            </a>
        </li>
        <li class="nav-item fs-5">
            <a href="{{ url('/ticket') }}" class="nav-link py-3 border-bottom @if(Request::path() == 'ticket') active @endif" title="{{ __('Tickets') }}" data-bs-toggle="tooltip" data-bs-placement="right">
                <i class="las la-tools fs-1"></i>
            </a>
        </li>
    </ul>
    <div class="dropdown border-top">
        <a href="#" class="d-flex align-items-center justify-content-center p-3 link-dark text-decoration-none dropdown-toggle" id="dropdownUser3" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="https://github.com/mdo.png" alt="mdo" width="24" height="24" class="rounded-circle">
        </a>
        <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser3">
            <li><a class="dropdown-item" href="{{ url('/profile') }}"><i class="las la-user-tie"></i> {{ __('Profile') }}</a></li>
            <li><a class="dropdown-item" href="{{ url('/setting') }}"><i class="las la-cogs"></i> {{ __('Settings') }}</a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item" onclick="Hammer.exit()" href="#"><i class="las la-door-open"></i> {{ __('Exit') }}</a>
            </li>
        </ul>
    </div>
</div>
