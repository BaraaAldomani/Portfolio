<x-layout :meta="$meta">
    {{-- ───────────── HERO ───────────── --}}
    <section class="relative">
        <div class="mx-auto grid max-w-7xl items-center gap-12 px-5 py-16 sm:px-8 sm:py-24 lg:grid-cols-[1.1fr_0.9fr]">
            <div data-load>
                <p data-load-item class="inline-flex items-center gap-2 rounded-full border border-line bg-surface px-3 py-1 text-xs font-bold text-primary-700">
                    <span class="relative flex h-2 w-2" aria-hidden="true">
                        <span class="absolute inline-flex h-full w-full motion-safe:animate-ping rounded-full bg-primary-400 opacity-75"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-primary"></span>
                    </span>
                    {{ setting_text('home.available', 'v3.available') }}
                </p>
                <p data-load-item class="mt-5 text-sm font-bold tracking-wide text-primary-700">{{ __('common.brand') }} · {{ setting_text('home.role', 'v3.role') }}</p>
                <h1 data-load-item class="mt-3 text-4xl font-extrabold leading-[1.08] tracking-tight text-ink sm:text-5xl lg:text-6xl">{{ setting_text('home.hero_title', 'v3.hero.title') }}</h1>
                <p data-load-item class="mt-6 max-w-xl text-lg leading-relaxed text-muted">{{ setting_text('home.hero_lead', 'v3.hero.lead') }}</p>
                <div data-load-item class="mt-8 flex flex-wrap items-center gap-3">
                    <a href="{{ route('contact') }}" class="js-magnetic btn-press glow-cyan inline-flex items-center justify-center rounded-full bg-primary px-7 py-3.5 text-sm font-bold text-[#08121b] transition-shadow hover:bg-primary-400">{{ setting_text('home.hero_primary', 'v3.hero.primary') }}</a>
                    <a href="{{ route('projects.index') }}" class="btn-press group inline-flex items-center gap-2 rounded-full border border-line bg-surface px-7 py-3.5 text-sm font-bold text-ink transition-colors hover:border-primary-300 hover:text-primary-700">
                        {{ setting_text('home.hero_secondary', 'v3.hero.secondary') }}
                        <svg class="h-4 w-4 transition-transform duration-200 group-hover:translate-x-1 rtl:-scale-x-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                    </a>
                </div>
            </div>

            <div data-load>
                <div data-load-item class="v3-frame relative mx-auto max-w-sm lg:mx-0 lg:ms-auto">
                    <div data-img-reveal class="v3-photo-flip overflow-hidden rounded-2xl">
                        <img src="{{ image_url(setting('images.portrait'), 'portfolio.images.portrait') }}" alt="{{ __('common.brand') }}, {{ setting_text('home.role', 'v3.role') }}"
                             width="640" height="800" decoding="async" class="v3-photo aspect-[4/5] w-full object-cover object-top">
                    </div>
                    @php($heroMetric = $site->metrics('home')->first())
                    <div class="glass absolute -bottom-5 rounded-2xl p-4 shadow-lg ltr:-start-5 rtl:-end-5">
                        <p class="text-2xl font-extrabold text-primary-700"><span data-counter="{{ $heroMetric?->value ?? 4 }}">0</span><span aria-hidden="true">{{ $heroMetric?->suffix ?? '+' }}</span></p>
                        <p class="mt-0.5 text-xs font-semibold leading-snug text-muted">{{ $heroMetric?->localized('label') ?? __('v3.metrics.0.label') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ───────────── PROOF + METRICS ───────────── --}}
    <section class="border-y border-line">
        <div class="mx-auto max-w-6xl px-5 py-16 sm:px-8 sm:py-20">
            <div class="reveal max-w-2xl">
                <h2 class="text-2xl font-extrabold text-ink sm:text-3xl">{{ setting_text('home.proof_title', 'v3.proof.title') }}</h2>
                <p class="mt-3 leading-relaxed text-muted">{{ setting_text('home.proof_lead', 'v3.proof.lead') }}</p>
            </div>
            <dl class="mt-12 grid grid-cols-2 gap-5 sm:grid-cols-3" data-stagger>
                @foreach ($site->metrics('home') as $m)
                    <div class="reveal glass glass-hover rounded-2xl p-6 text-center">
                        <dt class="text-4xl font-extrabold text-primary-700 sm:text-5xl"><span data-counter="{{ $m->value }}">0</span><span aria-hidden="true">{{ $m->suffix }}</span></dt>
                        <dd class="mt-1 text-sm font-semibold text-muted">{{ $m->localized('label') }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>

    {{-- ───────────── FOCUS (three cards, centre one focused) ───────────── --}}
    <section class="mx-auto max-w-6xl px-5 py-16 sm:px-8 sm:py-24">
        <div class="reveal max-w-xl">
            <h2 class="text-2xl font-extrabold text-ink sm:text-3xl">{{ setting_text('home.focus_title', 'v3.focus.title') }}</h2>
            <p class="mt-3 leading-relaxed text-muted">{{ setting_text('home.focus_lead', 'v3.focus.lead') }}</p>
        </div>
        <div class="mt-12 grid items-start gap-6 md:grid-cols-3" data-stagger>
            @foreach ($site->focusItems() as $item)
                <div class="reveal glass glass-hover group rounded-2xl p-7 {{ $loop->index === 1 ? 'glass-focus md:-mt-3 md:mb-3' : '' }}">
                    <span class="font-mono text-sm font-bold text-primary-700">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <h3 class="mt-4 text-lg font-extrabold text-ink transition-colors duration-200 group-hover:text-primary-700">{{ $item->localized('title') }}</h3>
                    <p class="mt-2 leading-relaxed text-muted">{{ $item->localized('blurb') }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ───────────── SELECTED WORK ───────────── --}}
    <section class="border-t border-line">
        <div class="mx-auto max-w-6xl px-5 py-16 sm:px-8 sm:py-24">
            <div class="flex flex-wrap items-end justify-between gap-6">
                <div class="reveal max-w-xl">
                    <h2 class="text-2xl font-extrabold text-ink sm:text-3xl">{{ setting_text('home.projects_title', 'home.projects.title') }}</h2>
                    <p class="mt-3 leading-relaxed text-muted">{{ setting_text('home.projects_lead', 'home.projects.lead') }}</p>
                </div>
                <a href="{{ route('projects.index') }}" class="reveal group inline-flex items-center gap-2 text-sm font-bold text-primary-700 hover:text-primary-800">
                    {{ __('common.view_all_projects') }}
                    <svg class="h-4 w-4 transition-transform duration-200 group-hover:translate-x-1 rtl:-scale-x-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                </a>
            </div>

            <div class="mt-12 grid gap-6 md:grid-cols-3" data-stagger>
                @foreach ($projects as $project)
                    <x-project-card :project="$project" class="reveal" />
                @endforeach
            </div>
        </div>
    </section>
</x-layout>
