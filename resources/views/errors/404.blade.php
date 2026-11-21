{{-- A page that does not exist, in the visitor's language and with the way
     back. No route matched, so the locale middleware never ran: the language
     is read from the address here (/en/... is English, anything else Arabic). --}}
@php
    $locales = config('app.supported_locales');
    $locale = in_array(request()->segment(1), $locales, true) ? request()->segment(1) : $locales[0];
    app()->setLocale($locale);
    \Illuminate\Support\Facades\URL::defaults(['locale' => $locale]);

    $meta = new \App\Support\Seo\PageMeta(
        title: __('site.pages.missing.title') . ' | ' . __('common.brand'),
        description: __('site.pages.missing.lead'),
        alternates: collect($locales)->mapWithKeys(fn (string $l): array => [$l => route('home', ['locale' => $l])])->all(),
        canonical: route('home'),
    );
@endphp
<x-layout :meta="$meta" page="missing" :noindex="true">
    <x-page-head class="!min-h-[80svh] !justify-center" :tag="'404'" :title="__('site.pages.missing.title')" :lead="__('site.pages.missing.lead')">
        <div class="mt-10 flex flex-wrap items-center gap-x-6 gap-y-4" data-reveal>
            <a href="{{ route('home') }}" class="cta">{{ __('common.back_home') }}</a>
            <a href="{{ route('services') }}" class="ulink">{{ __('site.nav.services') }}</a>
            <a href="{{ route('projects.index') }}" class="ulink">{{ __('site.nav.work') }}</a>
        </div>
    </x-page-head>
</x-layout>
