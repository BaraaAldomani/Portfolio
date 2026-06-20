<x-layout :meta="$meta">
    <x-section width="narrow">
        <a href="{{ route('projects.index') }}" class="reveal inline-flex items-center gap-2 text-sm font-bold text-primary-700 hover:text-primary-800">
            <svg class="h-4 w-4 ltr:-scale-x-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
            {{ __('common.view_all_projects') }}
        </a>

        <header class="mt-6">
            <p class="reveal text-xs font-bold uppercase tracking-wide text-primary-700">{{ $project->localized('sector') }}</p>
            <h1 data-reveal-text class="mt-2 text-3xl font-extrabold text-ink sm:text-4xl">{{ $project->localized('title') }}</h1>
            <p class="reveal mt-4 text-lg leading-relaxed text-muted">{{ $project->localized('summary') }}</p>

            <ul class="reveal mt-5 flex flex-wrap gap-2" aria-label="{{ __('common.highlights') }}">
                @foreach ($project->highlights() as $highlight)
                    <li class="rounded-full bg-primary-50 px-3 py-1 text-xs font-bold text-primary-700">{{ $highlight }}</li>
                @endforeach
            </ul>
        </header>

        {{-- Cover banner --}}
        <div class="reveal mt-10 overflow-hidden rounded-3xl border border-line shadow-xl">
            <img src="{{ $project->coverImage() }}"
                 alt="{{ $project->localized('title') }}"
                 width="1200" height="750" loading="lazy" decoding="async"
                 class="aspect-[16/10] w-full object-cover">
        </div>

        {{-- Case study --}}
        <div class="mt-14 space-y-10">
            @foreach (['problem', 'solution', 'result'] as $i => $section)
                <section class="reveal-start">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-surface-dark text-sm font-extrabold text-ink-on-dark" aria-hidden="true">{{ $i + 1 }}</span>
                        <h2 class="text-xl font-extrabold text-ink">{{ __('common.' . $section) }}</h2>
                    </div>
                    <p class="mt-3 ps-12 leading-relaxed text-ink/90">{{ $project->localized($section) }}</p>
                </section>
            @endforeach
        </div>
    </x-section>

    {{-- CTA --}}
    <x-section bg="dark" width="narrow" class="js-spotlight">
        <div class="text-center">
            <h2 class="reveal gradient-text text-2xl font-extrabold sm:text-3xl">{{ __('home.cta.title') }}</h2>
            <p class="reveal mt-3 text-muted-on-dark">{{ __('home.cta.lead') }}</p>
            <div class="reveal mt-7 flex justify-center">
                <x-cta-button :href="route('contact')">{{ __('home.cta.button') }}</x-cta-button>
            </div>
        </div>
    </x-section>
</x-layout>
