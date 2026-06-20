@props(['project'])
@php($cover = $project->coverImage())
<article {{ $attributes->class(['group v3-proj flex flex-col overflow-hidden rounded-2xl']) }}>
    <a href="{{ route('projects.show', ['project' => $project->slug()]) }}"
       class="relative block overflow-hidden rounded-t-2xl" tabindex="-1" aria-hidden="true">
        <img src="{{ $cover }}" alt="" width="1200" height="750" loading="lazy" decoding="async"
             class="aspect-[16/10] w-full object-cover transition-transform duration-500 group-hover:scale-105">
        {{-- Hover affordance: gradient wash + circular arrow (decorative; real link is below) --}}
        <span class="pointer-events-none absolute inset-0 bg-gradient-to-t from-surface-dark/70 via-surface-dark/10 to-transparent opacity-0 transition-opacity duration-500 group-hover:opacity-100"></span>
        <span class="pointer-events-none absolute bottom-3 inline-flex h-10 w-10 translate-y-2 items-center justify-center rounded-full bg-surface/95 text-primary-700 opacity-0 shadow-lg backdrop-blur-sm transition duration-500 group-hover:translate-y-0 group-hover:opacity-100 ltr:right-3 rtl:left-3">
            <svg class="h-4 w-4 rtl:-scale-x-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
        </span>
    </a>

    <div class="flex flex-1 flex-col p-6">
        <p class="text-xs font-bold uppercase tracking-wide text-primary-700">{{ $project->localized('sector') }}</p>
        <h3 class="mt-2 text-lg font-extrabold text-ink">{{ $project->localized('title') }}</h3>
        <p class="mt-2 flex-1 text-sm leading-relaxed text-muted">{{ $project->localized('summary') }}</p>

        <ul class="mt-4 flex flex-wrap gap-2" aria-label="{{ __('common.highlights') }}">
            @foreach ($project->highlights() as $highlight)
                <li class="rounded-full bg-surface-muted px-3 py-1 text-xs font-semibold text-muted">{{ $highlight }}</li>
            @endforeach
        </ul>

        <a href="{{ route('projects.show', ['project' => $project->slug()]) }}"
           class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-primary-700 hover:text-primary-800">
            {{ __('common.case_study') }}
            <svg class="h-4 w-4 rtl:-scale-x-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M5 12h14m-6-6 6 6-6 6" />
            </svg>
        </a>
    </div>
</article>
