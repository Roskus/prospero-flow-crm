@php $menu = include(resource_path('views'.DIRECTORY_SEPARATOR.'menu'.DIRECTORY_SEPARATOR.'menu.php')); @endphp

<div class="d-flex flex-column flex-shrink-0 bg-light d-none d-sm-block d-print-none sidebar rounded">
    <ul class="nav nav-pills nav-flush flex-column mb-auto ">
        
        @foreach ($menu as $item)
            @if(isset($item['is_drop_down']))
                {{-- DROPDOWN --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="d-flex align-items-center">
                            <i class="{{ $item['icon_class'] }} fs-2"></i>
                            <span class="ps-2">{{ $item['title'] }}</span>
                        </div>
                    </a>
                    <ul class="dropdown-menu">
                        @foreach($item['children'] as $child)
                            <li>
                                <a class="dropdown-item @if(Request::url() == $child['url']) active @endif fs-6" href="{{ $child['url'] }}">
                                    <div class="d-flex align-items-center">
                                        <i class="{{ $child['icon_class'] }}"></i>
                                        <span class="ps-2">{{ $child['title'] }}</span>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @else
                {{-- LINK --}}
                <li class="nav-item">
                    <a href="{{ $item['url'] }}" class="nav-link px-3 py-1  @if(Request::url() == $item['url']) active @endif" title="{{ $item['title'] }}" data-bs-toggle="tooltip" data-bs-placement="right">
                        <div class="d-flex align-items-center">
                            <i class="{{ $item['icon_class'] }} fs-2"></i>
                            <span class="ps-2">{{ $item['title'] }}</span>
                        </div>
                    </a>
                </li>
            @endif        
        @endforeach

    </ul>
    <div class="dropdown border-top">
        <a href="#" class="d-flex align-items-center justify-content-center p-3 link-dark text-decoration-none dropdown-toggle" id="dropdownUser3" data-bs-toggle="dropdown" aria-expanded="false">
            @if(empty(Auth::user()->photo))
            <img src="/asset/img/user.jpg" alt="{{ Auth::user()->first_name }}" width="32" height="32" class="rounded-circle">
            @else
            <img src="/asset/upload/company/{{ \Illuminate\Support\Str::slug(Auth::user()->company->name, '_') }}/{{ Auth::user()->photo }}" alt="{{ Auth::user()->first_name }}" width="32" height="32" class="rounded-circle">
            @endif
        </a>
        <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser3">
            <li><a class="dropdown-item" href="{{ url('/profile') }}"><i class="las la-user-tie"></i> {{ __('Profile') }}</a></li>
            <li><a class="dropdown-item" href="{{ url('/setting') }}"><i class="las la-cogs"></i> {{ __('Settings') }}</a></li>
            <li>
                <hr class="dropdown-divider">
            </li>
            <li>
                <a class="dropdown-item" onclick="Hammer.exit()" href="#"><i class="las la-door-open"></i> {{ __('Exit') }}</a>
            </li>
        </ul>
    </div>
</div>
