@props(['meta', 'page' => 'page', 'noindex' => false])
@php($locale = app()->getLocale())
@php($jsonLd = \App\Support\Seo\JsonLd::forPage($meta))
@php($shareImage = $meta->image ?: asset('images/og.png'))
@php($ogLocales = ['ar' => 'ar_SA', 'en' => 'en_US'])
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $meta->title }}</title>
    <meta name="description" content="{{ $meta->description }}">
    <link rel="canonical" href="{{ $meta->canonical }}">
    @foreach ($meta->alternates as $altLocale => $url)
        <link rel="alternate" hreflang="{{ $altLocale }}" href="{{ $url }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ $meta->alternates[config('app.locale')] ?? $meta->canonical }}">
    @if ($noindex)
        <meta name="robots" content="noindex, follow">
    @endif

    {{-- How the page looks when it is shared (WhatsApp, LinkedIn, X) --}}
    <meta property="og:type" content="{{ request()->routeIs('about') ? 'profile' : 'website' }}">
    <meta property="og:site_name" content="{{ __('common.brand') }}">
    <meta property="og:url" content="{{ $meta->canonical }}">
    <meta property="og:title" content="{{ $meta->title }}">
    <meta property="og:description" content="{{ $meta->description }}">
    <meta property="og:image" content="{{ $shareImage }}">
    @unless ($meta->image)
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
    @endunless
    <meta property="og:image:alt" content="{{ $meta->title }}">
    <meta property="og:locale" content="{{ $ogLocales[$locale] ?? 'ar_SA' }}">
    @foreach ($ogLocales as $code => $ogLocale)
        @if ($code !== $locale)
            <meta property="og:locale:alternate" content="{{ $ogLocale }}">
        @endif
    @endforeach
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $meta->title }}">
    <meta name="twitter:description" content="{{ $meta->description }}">
    <meta name="twitter:image" content="{{ $shareImage }}">

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="icon" href="{{ asset('favicon.svg') }}" sizes="any">
    <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}">

    <x-tracking-head />

    {{-- The two fonts the first screen is set in, fetched alongside the CSS
         instead of after it. --}}
    @unless (\Illuminate\Support\Facades\Vite::isRunningHot())
        @foreach ($locale === 'ar' ? ['alexandria/files/alexandria-arabic-wght-normal', 'readex-pro/files/readex-pro-arabic-full-normal'] : ['alexandria/files/alexandria-latin-wght-normal', 'readex-pro/files/readex-pro-latin-full-normal'] as $font)
            @php($fontUrl = rescue(fn () => \Illuminate\Support\Facades\Vite::asset("node_modules/@fontsource-variable/{$font}.woff2"), null, false))
            @if ($fontUrl)
                <link rel="preload" as="font" type="font/woff2" href="{{ $fontUrl }}" crossorigin>
            @endif
        @endforeach
    @endunless

    {{-- Motion hides content only under html.js, and the loader shows only on
         the first view of a session. If the bundle fails to boot within 4s
         (blocked, offline, old browser) both are undone so every word is
         visible. --}}
    <script>(function(d){d.classList.add('js');try{if(!sessionStorage.getItem('seen'))d.classList.add('is-loading')}catch(e){}setTimeout(function(){if(!window.__booted)d.classList.remove('js','is-loading')},4000)})(document.documentElement)</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Dashboard-managed accent (Theme settings). Everything else is fixed
         by the design system in resources/css/app.css. --}}
    <style>:root{--brand-primary:{{ setting('theme.primary', '#24abe3') }};}</style>
    <meta name="theme-color" content="#0f172a">

    <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
</head>
<body data-page="{{ $page }}" class="font-sans text-ink antialiased">
    <div class="loader" aria-hidden="true">
        <x-logo class="loader__mark" />
        <span class="loader__count" data-loader-count>0</span>
    </div>
    <div class="grain" aria-hidden="true"></div>

    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:start-4 focus:top-4 focus:z-[80] focus:rounded-full focus:bg-ink focus:px-5 focus:py-2.5 focus:text-bg">
        {{ __('common.skip_to_content') }}
    </a>

    <x-site-nav :alternates="$meta->alternates" />

    <main id="main">
        {{ $slot }}
    </main>

    <x-site-footer />

    {{-- Floating WhatsApp: the first-choice channel for clients in KSA. --}}
    <a href="{{ whatsapp_url() }}" target="_blank" rel="noopener" class="wa-fab" data-wa-fab data-track="whatsapp" data-track-from="floating" aria-label="{{ __('common.whatsapp_message') }}">
        <x-icon.whatsapp class="h-6 w-6" />
    </a>
</body>
</html>
