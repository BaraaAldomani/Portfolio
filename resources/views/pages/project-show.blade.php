<x-layout :meta="$meta" page="project">
    <article>
        {{-- ═════════ Head ═════════ --}}
        <header class="page-head">
            <a href="{{ route('projects.index') }}" class="ulink self-start text-sm" data-reveal>
                <svg class="arrow-fwd rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                {{ __('site.pages.project.back') }}
            </a>

            <p class="label mt-12" data-reveal>{{ __('site.pages.work.kinds.' . $project->kind) }} · <span class="num">{{ $project->year }}</span></p>
            <h1 class="d-hero mt-2" data-words>{{ $project->name() }}</h1>
            <p class="d-3 mt-2 text-muted" data-reveal>{{ $project->localized('title') }}</p>
        </header>

        <div class="wrap grid gap-x-10 gap-y-8 pb-16 lg:grid-cols-12">
            <p class="statement !text-[clamp(1.35rem,2.3vw,2.4rem)] lg:col-span-7" data-reveal>{{ $project->localized('summary') }}</p>

            <dl class="grid grid-cols-2 content-start gap-x-8 gap-y-6 lg:col-span-4 lg:col-start-9" data-reveal>
                <div>
                    <dt class="label">{{ __('site.pages.project.sector') }}</dt>
                    <dd class="mt-1 font-semibold">{{ $project->localized('sector') }}</dd>
                </div>
                @if ($project->year)
                    <div>
                        <dt class="label">{{ __('site.pages.project.year') }}</dt>
                        <dd class="num mt-1 font-semibold">{{ $project->year }}</dd>
                    </div>
                @endif
                @if ($project->localized('role'))
                    <div class="col-span-2">
                        <dt class="label">{{ __('site.pages.project.role') }}</dt>
                        <dd class="mt-1 font-semibold">{{ $project->localized('role') }}</dd>
                    </div>
                @endif
                @if ($project->live_url)
                    <div class="col-span-2 pt-2">
                        <a href="{{ $project->live_url }}" target="_blank" rel="noopener" data-track="project_visit" data-track-from="{{ $project->slug_en }}" class="cta">
                            {{ __('site.work.visit') }}
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17 17 7M9 7h8v8"/></svg>
                        </a>
                    </div>
                @endif
            </dl>
        </div>

        {{-- ═════════ Picture (or typography for confidential work) ═════════ --}}
        <div class="wrap">
            @if ($project->hasCover())
                <x-window :src="$project->coverImage()" :alt="$project->localized('title')" :name="$project->name()" data-reveal />
            @else
                <div class="typo-panel framed" data-reveal>
                    <p class="typo-panel__big">{{ $project->localized('stat') }}</p>
                    <p class="label">{{ $project->kind === 'gov' ? __('site.work.confidential') : $project->localized('sector') }}</p>
                </div>
            @endif
        </div>

        {{-- ═════════ The story: challenge → built → result ═════════ --}}
        <div class="sec">
            <div class="rows">
                @foreach (['problem' => 'challenge', 'solution' => 'solution', 'result' => 'result'] as $field => $label)
                    <section class="row row--3 !items-start" data-reveal>
                        <span class="row__n pt-3">{{ sprintf('%02d', $loop->iteration) }}</span>
                        <h2 class="row__title">{{ __('site.pages.project.' . $label) }}</h2>
                        <p class="lede leading-loose">{{ $project->localized($field) }}</p>
                    </section>
                @endforeach
            </div>

            <ul class="mt-12 flex flex-wrap gap-x-8 gap-y-3" aria-label="{{ __('common.highlights') }}" data-reveal>
                @foreach ($project->highlights() as $highlight)
                    <li class="flex items-center gap-3 font-semibold"><x-star class="h-2 w-2 text-accent" />{{ $highlight }}</li>
                @endforeach
            </ul>
        </div>

        {{-- ═════════ Next ═════════ --}}
        @if ($next)
            <a href="{{ route('projects.show', ['project' => $next->slug()]) }}" class="rule block px-[var(--pad)] py-16 lg:py-24" data-cursor="{{ __('site.work.open') }}">
                <p class="label">{{ __('site.work.next') }}</p>
                <p class="d-hero mt-2">{{ $next->name() }}</p>
            </a>
        @endif
    </article>
</x-layout>
