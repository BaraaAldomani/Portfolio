@props(['alternates'])
@php($links = [
    ['route' => 'projects.index', 'active' => 'projects.*', 'label' => __('site.nav.work')],
    ['route' => 'services', 'active' => 'services*', 'label' => __('site.nav.services')],
    ['route' => 'about', 'active' => 'about', 'label' => __('site.nav.about')],
])
@php($otherLocale = app()->getLocale() === 'ar' ? 'en' : 'ar')

<header class="nav">
    <a href="{{ route('home') }}" class="nav__brand" aria-label="{{ __('common.brand') }}">
        <x-logo class="nav__mark" />
        <span>{{ __('common.brand') }}</span>
    </a>

    <nav aria-label="{{ __('nav.main_navigation') }}" class="nav__links">
        @foreach ($links as $link)
            <a href="{{ route($link['route']) }}" @if (request()->routeIs($link['active'])) aria-current="page" @endif class="nav__link">{{ $link['label'] }}</a>
        @endforeach
    </nav>

    <div class="nav__end">
        <a href="{{ $alternates[$otherLocale] }}" hreflang="{{ $otherLocale }}" lang="{{ $otherLocale }}" class="nav__lang num">{{ $otherLocale === 'en' ? 'EN' : 'ع' }}</a>
        <a href="{{ route('contact') }}" data-track="cta_contact" data-track-from="nav" class="cta cta--sm hidden sm:inline-flex">{{ __('site.nav.start') }}</a>
        <button id="menu-toggle" type="button" aria-expanded="false" aria-controls="menu" class="nav__toggle framed">
            <span>{{ __('site.nav.menu') }}</span><i aria-hidden="true"></i>
        </button>
    </div>
</header>

<div id="menu" class="menu" inert>
    <ul>
        <li><a class="menu-link" style="--i:0" href="{{ route('home') }}">{{ __('nav.home') }}</a></li>
        @foreach ($links as $i => $link)
            <li><a class="menu-link" style="--i:{{ $i + 1 }}" href="{{ route($link['route']) }}">{{ $link['label'] }}</a></li>
        @endforeach
        <li><a class="menu-link" style="--i:4" href="{{ route('contact') }}">{{ __('site.nav.contact') }}</a></li>
    </ul>

    <div class="flex flex-wrap items-center gap-3">
        <a href="{{ whatsapp_url() }}" target="_blank" rel="noopener" data-track="whatsapp" data-track-from="menu" class="cta cta--wa">
            <x-icon.whatsapp class="h-5 w-5" />{{ __('common.whatsapp_message') }}
        </a>
        <a href="{{ $alternates[$otherLocale] }}" hreflang="{{ $otherLocale }}" lang="{{ $otherLocale }}" class="cta cta--line">{{ __('nav.switch_language') }}</a>
    </div>
</div>
