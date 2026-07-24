@props(['meta'])
@php($locale = app()->getLocale())
@php($jsonLd = \App\Support\Seo\JsonLd::siteGraph($meta->description))
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

    <meta property="og:type" content="profile">
    <meta property="og:title" content="{{ $meta->title }}">
    <meta property="og:description" content="{{ $meta->description }}">
    <meta property="og:image" content="{{ asset('images/og-default.png') }}">

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="icon" href="{{ asset('favicon.svg') }}" sizes="any">
    <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}">

    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link href="https://fonts.bunny.net/css?family=hanken-grotesk:400,500,600,700,800|jetbrains-mono:400,500|cairo:400,600,700&display=swap" rel="stylesheet">

    <script>document.documentElement.classList.add('js')</script>
    @vite(['resources/css/app.css', 'resources/css/v3.css', 'resources/js/app.js', 'resources/js/v3.js'])

    {{-- Dashboard-managed brand colours. These override the three base tokens in
         tokens.css; every derived shade/surface/glow recomputes via color-mix(). --}}
    <style>:root{--brand-primary:{{ setting('theme.primary', '#4f46e5') }};--brand-secondary:{{ setting('theme.secondary', '#0f172a') }};--brand-accent:{{ setting('theme.accent', '#14b8a6') }};}</style>

    <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
</head>
<body class="relative bg-space font-sans text-ink antialiased">
    <div id="scroll-progress" class="scroll-progress" aria-hidden="true"></div>
    <div id="cursor-glow" aria-hidden="true"></div>

    {{-- Full-document UML / Git architecture watermark, built + sized by v3.js. --}}
    <div class="netbg" id="netbg" aria-hidden="true"></div>

    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:start-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-primary focus:px-4 focus:py-2 focus:text-white">
        {{ __('common.skip_to_content') }}
    </a>

    <x-site-nav :alternates="$meta->alternates" />

    <main id="main">
        {{ $slot }}
    </main>

    <x-site-footer />
</body>
</html>
