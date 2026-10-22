@extends('layouts.app')

@section('content')
@php
    $user = Auth::user();
    $isSuperAdmin = $user->hasRole('SuperAdmin');
    $settingGroups = [
        [
            'title' => __('Organization'),
            'icon' => 'las la-sitemap',
            'items' => [
                ['url' => url('/company'), 'icon' => 'las la-industry', 'label' => __('Companies'), 'visible' => $user->can('read company')],
                ['url' => url('/branch'), 'icon' => 'las la-store-alt', 'label' => __('Branches'), 'visible' => $user->can('read company')],
                ['url' => url('/user'), 'icon' => 'las la-users', 'label' => __('Users'), 'visible' => $user->can('read user')],
                ['url' => url('/permission'), 'icon' => 'las la-lock-open', 'label' => __('Permissions'), 'visible' => $isSuperAdmin],
            ],
        ],
        [
            'title' => __('Finance'),
            'icon' => 'las la-coins',
            'items' => [
                ['url' => url('/bank-account'), 'icon' => 'las la-piggy-bank', 'label' => __('Bank accounts'), 'visible' => true],
                ['url' => url('/bank'), 'icon' => 'las la-university', 'label' => __('Banks'), 'visible' => $isSuperAdmin],
                ['url' => url('/currency'), 'icon' => 'las la-euro-sign', 'label' => __('Currencies'), 'visible' => true],
                ['url' => url('/transaction/category'), 'icon' => 'las la-tags', 'label' => __('Transaction categories'), 'visible' => true],
            ],
        ],
        [
            'title' => __('Catalog'),
            'icon' => 'las la-boxes',
            'items' => [
                ['url' => url('/brand'), 'icon' => 'las la-apple-alt', 'label' => __('Brands'), 'visible' => true],
                ['url' => url('/category'), 'icon' => 'las la-archive', 'label' => __('Categories'), 'visible' => true],
                ['url' => url('/product'), 'icon' => 'las la-box', 'label' => __('Products'), 'visible' => $user->can('read product')],
            ],
        ],
        [
            'title' => __('Communication'),
            'icon' => 'las la-comments',
            'items' => [
                ['url' => url('/email-template'), 'icon' => 'las la-envelope-open-text', 'label' => __('Email templates'), 'visible' => true],
            ],
        ],
    ];
@endphp

<header>
   <h1>{{ __('Settings') }}</h1>
</header>

<div class="row g-3">
    @foreach($settingGroups as $settingGroup)
        @php($visibleItems = array_filter($settingGroup['items'], fn (array $item): bool => $item['visible']))
        @continue(empty($visibleItems))
        <div class="col-12 col-lg-6">
            <section class="card h-100">
                <h2 class="card-header bg-transparent h5 fw-semibold mb-0">
                    <i class="{{ $settingGroup['icon'] }}"></i>
                    {{ $settingGroup['title'] }}
                </h2>
                <div class="card-body">
                    <div class="row row-cols-1 row-cols-sm-2 g-2">
                        @foreach($visibleItems as $item)
                            <div class="col">
                                <a href="{{ $item['url'] }}" class="setting-tile d-flex align-items-center gap-3 h-100 px-3 py-2 border rounded text-body text-decoration-none">
                                    <i class="{{ $item['icon'] }} fs-2 text-primary"></i>
                                    <span>{{ $item['label'] }}</span>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        </div>
    @endforeach
</div>
@endsection
