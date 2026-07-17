<x-layout :meta="$meta">
    {{-- ───────────────────────── HERO ───────────────────────── --}}
    <x-section bg="dark" spacing="hero">
        <div class="mx-auto max-w-3xl text-center">
            <p class="reveal inline-flex items-center gap-2 rounded-full border border-line-on-dark px-3 py-1 text-xs font-bold text-accent-300">
                <span class="relative flex h-2 w-2" aria-hidden="true">
                    <span class="absolute inline-flex h-full w-full motion-safe:animate-ping rounded-full bg-accent-400 opacity-75"></span>
                    <span class="relative inline-flex h-2 w-2 rounded-full bg-accent-400"></span>
                </span>
                {{ setting_text('services_page.badge', 'services.badge') }}
            </p>

            <p class="reveal mt-5 text-sm font-bold tracking-wide text-accent-300">{{ setting_text('services_page.eyebrow', 'services.eyebrow') }}</p>
            <h1 data-reveal-text class="mt-3 text-4xl font-extrabold leading-[1.15] text-ink-on-dark sm:text-5xl">
                {{ setting_text('services_page.title', 'services.title') }}
            </h1>
            <p class="reveal mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-muted-on-dark">{{ setting_text('services_page.lead', 'services.lead') }}</p>

            <div class="reveal mt-9 flex flex-wrap items-center justify-center gap-4">
                <x-cta-button :href="route('contact')">{{ __('common.start_project') }}</x-cta-button>
                <x-cta-button :href="route('projects.index')" variant="ghost">{{ __('common.view_work') }}</x-cta-button>
            </div>

        </div>
    </x-section>

    {{-- ──────────────────── SERVICE FEATURE ROWS ──────────────────── --}}
    @php
        // Per-service icon chips (presentational only; keyed to service icon_key).
        $serviceIcons = [
            'systems' => '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/>',
            'web_apps' => '<rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8m-4-4v4"/>',
            'websites' => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a15 15 0 0 1 0 18 15 15 0 0 1 0-18Z"/>',
            'apis' => '<path d="M9 7H7a5 5 0 0 0 0 10h2m6-10h2a5 5 0 0 1 0 10h-2M8 12h8"/>',
        ];
    @endphp
    <x-section id="offer">
        <x-section-heading
            :eyebrow="setting_text('services_page.offer_eyebrow', 'services.offer.eyebrow')"
            :title="setting_text('services_page.offer_title', 'services.offer.title')"
            :lead="setting_text('services_page.offer_lead', 'services.offer.lead')"
            class="mx-auto text-center"
        />

        <div class="mt-16 space-y-20 sm:mt-20 sm:space-y-28">
            @foreach ($site->services() as $service)
                <div class="grid items-center gap-10 lg:grid-cols-2 lg:gap-16">
                    {{-- Illustration --}}
                    <div data-parallax data-parallax-speed="0.05" class="{{ $loop->even ? 'lg:order-2' : '' }}">
                        <div class="reveal">
                            <div class="group js-tilt glow-border relative overflow-hidden rounded-3xl border border-line bg-surface-muted shadow-xl transition-shadow duration-500 hover:shadow-2xl">
                                <img src="{{ $service->imageUrl() }}"
                                     alt="{{ $service->localized('image_alt') }}"
                                     width="800" height="600" loading="lazy" decoding="async"
                                     class="aspect-[4/3] h-full w-full object-cover transition-transform duration-700 ease-out motion-safe:group-hover:scale-[1.03]">
                            </div>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div data-stagger>
                        <div class="reveal-start flex items-center gap-3">
                            <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary-50 text-primary-700 ring-1 ring-inset ring-primary-100" aria-hidden="true">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $serviceIcons[$service->icon_key] ?? $serviceIcons['systems'] !!}</svg>
                            </span>
                            <span class="text-sm font-bold text-accent-600">{{ $service->localized('tag') }}</span>
                        </div>
                        <h2 class="reveal-start mt-4 text-2xl font-extrabold text-ink sm:text-3xl">{{ $service->localized('title') }}</h2>
                        <p class="reveal-start mt-4 text-lg leading-relaxed text-muted">{{ $service->localized('description') }}</p>

                        <ul class="mt-7 space-y-3">
                            @foreach ($service->points() as $point)
                                <li class="reveal flex items-center gap-3 text-sm font-semibold text-ink">
                                    <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-primary-50 text-primary-700" aria-hidden="true">
                                        <svg class="js-draw h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg>
                                    </span>
                                    {{ $point }}
                                </li>
                            @endforeach
                        </ul>

                        <a href="{{ route('contact') }}" class="reveal-start group/link mt-7 inline-flex items-center gap-2 text-sm font-bold text-primary-700 hover:text-primary-800">
                            {{ setting_text('services_page.discuss', 'services.discuss') }}
                            <svg class="h-4 w-4 transition-transform duration-300 group-hover/link:translate-x-1 rtl:-scale-x-100 rtl:group-hover/link:-translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </x-section>

    {{-- ───────────────────────── STATS ───────────────────────── --}}
    <x-section bg="dark">
        <div class="text-center">
            <p class="reveal text-sm font-bold tracking-wide text-accent-300">{{ setting_text('services_page.stats_eyebrow', 'services.stats.eyebrow') }}</p>
            <h2 class="reveal mt-2 text-2xl font-extrabold text-ink-on-dark sm:text-3xl">{{ setting_text('services_page.stats_title', 'services.stats.title') }}</h2>
        </div>

        <dl class="mt-14 grid grid-cols-2 gap-6 lg:grid-cols-4" data-stagger>
            @foreach ($site->metrics('services') as $stat)
                <div class="reveal rounded-2xl border border-line-on-dark bg-surface-dark-soft p-6 text-center">
                    <dd class="gradient-text text-4xl font-extrabold sm:text-5xl">
                        <span data-counter="{{ $stat->value }}">0</span><span aria-hidden="true">{{ $stat->suffix }}</span>
                    </dd>
                    <dt class="mt-2 text-xs font-semibold text-muted-on-dark sm:text-sm">{{ $stat->localized('label') }}</dt>
                </div>
            @endforeach
        </dl>
    </x-section>

    {{-- ───────────────────────── PROCESS ───────────────────────── --}}
    <x-section bg="muted">
        <div class="text-center">
            <p class="reveal text-sm font-bold tracking-wide text-primary-700">{{ setting_text('services_page.process_eyebrow', 'services.process.eyebrow') }}</p>
            <h2 class="reveal mt-2 text-2xl font-extrabold text-ink sm:text-3xl">{{ setting_text('services_page.process_title', 'services.process.title') }}</h2>
        </div>

        <div class="relative mt-16">
            {{-- Connecting line (drawn in on desktop) --}}
            <svg class="js-draw pointer-events-none absolute left-[12.5%] top-7 hidden w-3/4 lg:block" height="4" viewBox="0 0 1000 4" preserveAspectRatio="none" aria-hidden="true">
                <line x1="2" y1="2" x2="998" y2="2" stroke="var(--brand-accent)" stroke-width="3" stroke-linecap="round"/>
            </svg>

            <ol class="relative grid gap-10 sm:grid-cols-2 lg:grid-cols-4" data-stagger>
                @foreach ($site->processSteps() as $i => $step)
                    <li class="reveal text-center">
                        <span class="relative z-10 mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-primary text-lg font-extrabold text-white shadow-lg shadow-primary-600/30" aria-hidden="true">
                            {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
                        </span>
                        <h3 class="mt-5 text-lg font-extrabold text-ink">{{ $step->localized('title') }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-muted">{{ $step->localized('blurb') }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </x-section>

    {{-- ───────────────────── TECH MARQUEE ───────────────────── --}}
    <x-section spacing="tight">
        @php($tech = $site->technologies())
        <p class="reveal text-center text-sm font-bold tracking-wide text-muted">{{ setting_text('services_page.tech_title', 'services.tech_title') }}</p>
        <div class="js-marquee mt-8">
            <div class="js-marquee__track gap-3 py-1">
                @foreach ($tech as $t)
                    <span class="inline-flex items-center whitespace-nowrap rounded-full border border-line bg-surface px-5 py-2.5 text-sm font-bold text-ink">{{ $t->name }}</span>
                @endforeach
                @foreach ($tech as $t)
                    <span class="inline-flex items-center whitespace-nowrap rounded-full border border-line bg-surface px-5 py-2.5 text-sm font-bold text-ink" aria-hidden="true">{{ $t->name }}</span>
                @endforeach
            </div>
        </div>
    </x-section>

    {{-- ───────────────────────── CTA ───────────────────────── --}}
    <x-section bg="dark" width="narrow">
        <div class="text-center">
            <h2 class="reveal gradient-text text-3xl font-extrabold sm:text-4xl">{{ setting_text('services_page.cta_title', 'services.cta.title') }}</h2>
            <p class="reveal mx-auto mt-4 max-w-xl text-lg leading-relaxed text-muted-on-dark">{{ setting_text('services_page.cta_lead', 'services.cta.lead') }}</p>
            <div class="reveal mt-9 flex justify-center">
                <x-cta-button :href="route('contact')">{{ __('common.start_project') }}</x-cta-button>
            </div>
        </div>
    </x-section>
</x-layout>
