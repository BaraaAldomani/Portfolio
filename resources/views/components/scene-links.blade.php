@props(['project'])
<div {{ $attributes->class('scene__links') }}>
    <a href="{{ route('projects.show', ['project' => $project->slug()]) }}" class="ulink" data-track="project_story" data-track-from="{{ $project->slug_en }}">
        {{ __('site.worlds.story') }}
        <svg class="arrow-fwd" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
    </a>
    @if ($project->live_url)
        <a href="{{ $project->live_url }}" target="_blank" rel="noopener" class="ulink" data-track="project_visit" data-track-from="{{ $project->slug_en }}">
            {{ __('site.worlds.visit') }}
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17 17 7M9 7h8v8"/></svg>
        </a>
    @endif
</div>
