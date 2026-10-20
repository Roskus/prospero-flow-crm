@extends('layouts.app')

@push('styles')
<style>
    .setting-group .card-header {
        background: transparent;
        font-weight: 600;
    }
    .setting-tile {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .75rem 1rem;
        border: 1px solid var(--bs-border-color);
        border-radius: var(--bs-border-radius);
        color: var(--bs-body-color);
        text-decoration: none;
        height: 100%;
        transition: border-color .15s, background-color .15s;
    }
    .setting-tile:hover,
    .setting-tile:focus {
        border-color: var(--bs-primary);
        background-color: var(--bs-primary-bg-subtle);
        color: var(--bs-body-color);
    }
    .setting-tile i {
        font-size: 1.75rem;
        color: var(--bs-primary);
    }
</style>
@endpush

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
            <section class="card setting-group h-100">
                <h2 class="card-header h5 mb-0">
                    <i class="{{ $settingGroup['icon'] }}"></i>
                    {{ $settingGroup['title'] }}
                </h2>
                <div class="card-body">
                    <div class="row row-cols-1 row-cols-sm-2 g-2">
                        @foreach($visibleItems as $item)
                            <div class="col">
                                <a href="{{ $item['url'] }}" class="setting-tile">
                                    <i class="{{ $item['icon'] }}"></i>
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
