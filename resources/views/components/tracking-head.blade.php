{{-- Search-engine ownership tags + analytics, all managed from the dashboard
     (Analytics & verification). Accepts either the bare code or the full
     <meta> tag pasted from the console. Analytics only load in production. --}}
@php
    $verificationCode = function (?string $raw): ?string {
        $raw = trim((string) $raw);
        if ($raw === '') {
            return null;
        }
        if (preg_match('/content\s*=\s*["\']([^"\']+)["\']/i', $raw, $m)) {
            return $m[1];
        }

        return $raw;
    };
    $google = $verificationCode(setting('tracking.google_verification'));
    $bing = $verificationCode(setting('tracking.bing_verification'));
    $umamiId = setting('tracking.umami_website_id');
    $umamiSrc = setting('tracking.umami_script_url') ?: \App\Filament\Pages\TrackingSettings::UMAMI_DEFAULT_SCRIPT;
    $ga4 = setting('tracking.ga4_id');
@endphp
@if ($google)
    <meta name="google-site-verification" content="{{ $google }}">
@endif
@if ($bing)
    <meta name="msvalidate.01" content="{{ $bing }}">
@endif
@production
    @if ($umamiId)
        <script defer src="{{ $umamiSrc }}" data-website-id="{{ $umamiId }}"></script>
    @endif
    @if ($ga4)
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ urlencode($ga4) }}"></script>
        <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag('js',new Date());gtag('config',@json($ga4));</script>
    @endif
@endproduction
