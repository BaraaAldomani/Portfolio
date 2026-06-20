@props(['alternates'])
@php($links = [
    ['route' => 'home', 'active' => 'home', 'label' => __('nav.home')],
    ['route' => 'services', 'active' => 'services', 'label' => __('nav.services')],
    ['route' => 'projects.index', 'active' => 'projects.*', 'label' => __('nav.projects')],
    ['route' => 'about', 'active' => 'about', 'label' => __('nav.about')],
    ['route' => 'contact', 'active' => 'contact', 'label' => __('nav.contact')],
])
@php($otherLocale = app()->getLocale() === 'ar' ? 'en' : 'ar')

<header class="sticky top-0 z-40 border-b border-line bg-surface/80 backdrop-blur-md">
    <nav aria-label="{{ __('nav.main_navigation') }}" class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-5 py-3 sm:px-8">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <img src="{{ asset(config('portfolio.images.logo')) }}" alt="" width="40" height="36" class="h-9 w-auto">
            <span class="flex flex-col leading-tight">
                <span class="text-[15px] font-extrabold text-ink">{{ __('common.brand') }}</span>
                <span class="text-[11px] font-semibold text-muted">{{ __('v3.role') }} · {{ __('common.footer_location') }}</span>
            </span>
        </a>

        <ul class="hidden items-center gap-7 lg:flex">
            @foreach ($links as $link)
                <li>
                    <a href="{{ route($link['route']) }}" @if (request()->routeIs($link['active'])) aria-current="page" @endif
                       class="nav-link text-sm font-semibold {{ request()->routeIs($link['active']) ? 'text-primary-700' : 'text-muted hover:text-ink' }}">{{ $link['label'] }}</a>
                </li>
            @endforeach
        </ul>

        <div class="flex items-center gap-2.5">
            <a href="{{ $alternates[$otherLocale] }}" hreflang="{{ $otherLocale }}" class="hidden text-sm font-bold text-muted hover:text-primary-700 sm:inline">{{ __('nav.switch_language') }}</a>

            <a href="{{ route('contact') }}" class="btn-press glow-cyan hidden items-center gap-1.5 rounded-full bg-primary px-4 py-2 text-sm font-bold text-[#08121b] transition-shadow hover:bg-primary-400 sm:inline-flex">
                {{ __('v3.cta.primary') }}
            </a>

            <button id="nav-toggle" type="button" aria-expanded="false" aria-controls="mobile-menu" aria-label="{{ __('nav.open_menu') }}"
                    class="btn-press rounded-lg border border-line p-2 text-ink lg:hidden">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
            </button>
        </div>
    </nav>

    <div id="mobile-menu" class="hidden border-t border-line lg:hidden">
        <ul class="space-y-1 px-5 py-3">
            @foreach ($links as $link)
                <li>
                    <a href="{{ route($link['route']) }}" class="block rounded-lg px-3 py-2 text-sm font-semibold {{ request()->routeIs($link['active']) ? 'bg-primary-50 text-primary-700' : 'text-muted hover:bg-surface-muted hover:text-ink' }}">{{ $link['label'] }}</a>
                </li>
            @endforeach
            <li class="pt-2"><a href="{{ route('contact') }}" class="block rounded-lg bg-primary px-3 py-2.5 text-center text-sm font-bold text-white">{{ __('v3.cta.primary') }}</a></li>
            <li class="px-3 pt-3 text-xs font-bold">
                <a href="{{ $alternates[$otherLocale] }}" class="text-muted">{{ __('nav.switch_language') }}</a>
            </li>
        </ul>
    </div>
</header>
