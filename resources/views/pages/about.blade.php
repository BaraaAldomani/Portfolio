<x-layout :meta="$meta" page="about">
    @php
        $bioRaw = setting('about.bio_' . app()->getLocale());
        $bio = $bioRaw ? preg_split('/\n\n+/', trim($bioRaw)) : (array) __('about.bio');
    @endphp

    <x-page-head :tag="__('site.pages.about.tag')" :title="setting_text('about.title', 'about.title')" />

    <section class="wrap grid gap-x-10 gap-y-12 pb-24 lg:grid-cols-12">
        <figure class="intro__portrait !max-w-none lg:col-span-4" data-reveal>
            <img src="{{ image_url(setting('images.about_portrait'), 'portfolio.images.about_portrait') }}"
                 alt="{{ __('common.brand') }}" width="915" height="921" decoding="async" data-drift="-12">
        </figure>

        <div class="lg:col-span-7 lg:col-start-6">
            <p class="statement !text-[clamp(1.35rem,2.4vw,2.5rem)]" data-reveal>{{ setting_text('about.lead', 'about.lead') }}</p>
            <div class="mt-8 max-w-2xl space-y-5 text-muted" data-reveal>
                @foreach ($bio as $paragraph)
                    <p>{{ $paragraph }}</p>
                @endforeach
            </div>
            <div class="mt-10 flex flex-wrap items-center gap-x-6 gap-y-3" data-reveal>
                <a href="{{ route('contact') }}" data-track="cta_contact" data-track-from="about" class="cta">{{ __('site.nav.start') }}</a>
                <a href="{{ asset(config('portfolio.cv_path')) }}" download data-track="cv" data-track-from="about" class="ulink">{{ __('common.download_cv') }}</a>
            </div>
        </div>
    </section>

    {{-- ═════════ Where I have worked ═════════ --}}
    <section class="sec">
        <header class="mb-14 grid gap-6 lg:grid-cols-12">
            <p class="label lg:col-span-3" data-reveal>{{ __('site.pages.about.experience') }}</p>
            <h2 class="d-2 lg:col-span-9" data-words>{{ setting_text('about.work_title', 'v3.work.title') }}</h2>
        </header>

        <div class="rows">
            @foreach ($site->experiences() as $item)
                <div class="row !items-start" data-reveal>
                    <span class="row__n pt-3">{{ sprintf('%02d', $loop->iteration) }}</span>
                    <div>
                        <h3 class="row__title !text-[clamp(1.4rem,2.8vw,2.8rem)]">{{ $item->localized('org') }}</h3>
                        <p class="d-3 mt-2 !text-[clamp(1rem,1.3vw,1.3rem)] text-muted">{{ $item->localized('role') }}</p>
                    </div>
                    <p class="row__sub">{{ $item->localized('blurb') }}</p>
                    <p class="label num">{{ $item->localized('period') }}</p>
                </div>
            @endforeach
        </div>
    </section>

    @include('home.method')

    {{-- ═════════ In short: the questions people (and assistants) ask about him ═════════ --}}
    <section class="sec !pt-0">
        <div class="grid gap-x-8 gap-y-10 lg:grid-cols-12">
            <h2 class="d-2 lg:col-span-5" data-words>{{ __('site.pages.about.short') }}</h2>
            <div class="lg:col-span-7">
                @foreach ((array) __('seo.faq') as $item)
                    <details class="faq" data-reveal @if ($loop->first) open @endif>
                        <summary>{{ $item['q'] }}<span class="faq__x" aria-hidden="true"></span></summary>
                        <p>{{ $item['a'] }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═════════ Capabilities + education ═════════ --}}
    <section class="sec !pt-0">
        <div class="grid gap-x-10 gap-y-12 lg:grid-cols-12">
            <div class="lg:col-span-7">
                <h2 class="d-2" data-words>{{ setting_text('about.capabilities_title', 'about.capabilities.title') }}</h2>
                <ul class="mt-8 flex flex-wrap gap-x-7 gap-y-3" data-reveal>
                    @foreach ($site->capabilities() as $cap)
                        <li class="flex items-center gap-3 font-semibold"><x-star class="h-2 w-2 text-accent" />{{ $cap->localized('label') }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="lg:col-span-4 lg:col-start-9" data-reveal>
                <p class="label">{{ setting_text('about.education_title', 'about.education.title') }}</p>
                <p class="d-3 mt-3">{{ setting_text('about.education_degree', 'about.education.degree') }}</p>
                <p class="mt-1 text-muted">{{ setting_text('about.education_school', 'about.education.school') }}</p>
                <p class="mt-4 font-medium">{{ setting_text('about.education_languages', 'about.education.languages') }}</p>
            </div>
        </div>
    </section>
</x-layout>
