<x-layout :meta="$meta">
    {{-- ───────────── INTRO ───────────── --}}
    <section class="mx-auto max-w-6xl px-5 py-16 sm:px-8 sm:py-24">
        <div class="grid items-center gap-12 lg:grid-cols-[1.1fr_0.9fr]">
            <div data-load>
                <p data-load-item class="text-sm font-bold tracking-wide text-primary-700">{{ setting_text('about.eyebrow', 'about.eyebrow') }}</p>
                <h1 data-load-item class="mt-3 text-4xl font-extrabold leading-[1.08] tracking-tight text-ink sm:text-5xl">{{ setting_text('about.title', 'about.title') }}</h1>
                <p data-load-item class="mt-6 text-lg leading-relaxed text-muted">{{ setting_text('about.lead', 'about.lead') }}</p>
                <div data-load-item class="mt-5 space-y-4 leading-relaxed text-muted">
                    @php
                        $bioRaw = setting('about.bio_' . app()->getLocale());
                        $bio = $bioRaw ? preg_split('/\n\n+/', trim($bioRaw)) : (array) __('about.bio');
                    @endphp
                    @foreach ($bio as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                </div>
            </div>
            <div data-load>
                <div data-load-item data-img-reveal class="mx-auto max-w-sm lg:ms-auto">
    <img
        src="{{ image_url(setting('images.about_portrait'), 'portfolio.images.about_portrait') }}"
        alt="{{ __('common.brand') }}, {{ setting_text('home.role', 'v3.role') }}"
        width="915"
        height="921"
        loading="lazy"
        decoding="async"
        class="w-full object-contain bg-transparent">

</div>
            </div>
        </div>
    </section>

    {{-- ───────────── WHERE I HAVE WORKED ───────────── --}}
    <section class="border-t border-line">
        <div class="mx-auto max-w-4xl px-5 py-16 sm:px-8 sm:py-24">
            <h2 class="reveal text-3xl font-extrabold tracking-tight text-ink sm:text-4xl">{{ setting_text('about.work_title', 'v3.work.title') }}</h2>
            <p class="reveal mt-3 leading-relaxed text-muted">{{ setting_text('about.work_lead', 'v3.work.lead') }}</p>

            <ol class="mt-12 border-s-2 border-line ps-8" data-stagger>
                @foreach ($site->experiences() as $item)
                    <li class="reveal-start group relative -ms-3 rounded-xl px-3 pb-8 pt-1 transition-colors duration-300 last:pb-1 hover:bg-surface-muted">
                        <span class="v3-node absolute -start-[1.45rem] top-2 h-5 w-5 rounded-full border-2 border-primary bg-surface ring-4 ring-surface" aria-hidden="true"></span>
                        <div class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1">
                            <h3 class="text-lg font-extrabold text-ink">
                                {{ $item->localized('role') }} <span class="text-muted">·</span>
                                <a href="{{ $item->url }}" target="_blank" rel="noopener" class="text-primary-700 underline-offset-4 transition-colors hover:text-primary-800 hover:underline">{{ $item->localized('org') }}</a>
                            </h3>
                            <span class="font-mono text-xs font-bold text-muted">{{ $item->localized('period') }}</span>
                        </div>
                        <p class="mt-2 leading-relaxed text-muted">{{ $item->localized('blurb') }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- ───────────── RAKEEZ (FOUNDER) ───────────── --}}
    @php($rakeezUrl = setting('rakeez.url', __('v3.rakeez.url')))
    <section class="border-y border-line">
        <div class="mx-auto max-w-6xl px-5 py-16 sm:px-8 sm:py-24">
            <div class="grid items-center gap-10 lg:grid-cols-2">
                <div class="reveal-start">
                    <p class="inline-flex items-center gap-1.5 rounded-full border border-primary-200 bg-primary-50 px-3 py-1 text-xs font-bold text-primary-700">{{ setting_text('rakeez.eyebrow', 'v3.rakeez.eyebrow') }}</p>
                    <h2 class="mt-4 text-3xl font-extrabold tracking-tight text-ink sm:text-4xl">{{ setting('rakeez.brand_en', __('v3.rakeez.title')) }} <span class="font-normal text-muted">|</span> {{ setting('rakeez.brand_ar', __('v3.rakeez.title_ar')) }}</h2>
                    <p class="mt-4 text-lg leading-relaxed text-muted">{{ setting_text('rakeez.lead', 'v3.rakeez.lead') }}</p>
                    <ul class="mt-6 space-y-2.5">
                        @foreach (setting_list('rakeez.points', 'v3.rakeez.points') as $pt)
                            <li class="flex items-center gap-3 font-semibold text-ink"><span class="text-primary">+</span>{{ $pt }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ $rakeezUrl }}" target="_blank" rel="noopener" class="js-magnetic btn-press mt-7 inline-flex items-center gap-2 rounded-full bg-primary px-6 py-3 text-sm font-bold text-[#08121b] transition-colors hover:bg-primary-400">
                        {{ setting_text('rakeez.cta', 'v3.rakeez.cta') }}
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17 17 7M9 7h8v8"/></svg>
                    </a>
                </div>
                <div class="reveal flex items-center justify-center">
                    <a href="{{ $rakeezUrl }}" target="_blank" rel="noopener"
                       class="group inline-flex" aria-label="{{ setting('rakeez.brand_en', __('v3.rakeez.title')) }} — {{ setting_text('rakeez.cta', 'v3.rakeez.cta') }}">
                        <img src="{{ image_url(setting('images.rakeez_logo'), 'portfolio.images.rakeez_logo') }}" alt="Rakeez"
                             width="320" height="320" class="rakeez-logo">
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ───────────── CAPABILITIES + EDUCATION ───────────── --}}
    <section class="border-t border-line">
        <div class="mx-auto max-w-6xl px-5 py-16 sm:px-8 sm:py-24">
            <h2 class="reveal text-2xl font-extrabold text-ink sm:text-3xl">{{ setting_text('about.capabilities_title', 'about.capabilities.title') }}</h2>
            <ul class="mt-8 grid gap-3 sm:grid-cols-2" data-stagger>
                @foreach ($site->capabilities() as $cap)
                    <li class="reveal v3-lift flex items-center gap-3 rounded-xl border border-line bg-surface p-4 transition-colors hover:border-primary-200">
                        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-primary-50 text-primary-700" aria-hidden="true">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg>
                        </span>
                        <span class="font-semibold text-ink">{{ $cap->localized('label') }}</span>
                    </li>
                @endforeach
            </ul>

            <div class="reveal mt-10 rounded-2xl border border-line bg-surface-muted p-7 sm:p-8">
                <h3 class="text-xl font-extrabold text-ink">{{ setting_text('about.education_title', 'about.education.title') }}</h3>
                <p class="mt-4 font-bold text-ink">{{ setting_text('about.education_degree', 'about.education.degree') }}</p>
                <p class="mt-1 text-sm text-muted">{{ setting_text('about.education_school', 'about.education.school') }}</p>
                <p class="mt-4 text-sm font-semibold text-primary-700">{{ setting_text('about.education_languages', 'about.education.languages') }}</p>
            </div>
        </div>
    </section>
</x-layout>
