<x-layout :meta="$meta" page="work">
    @php($scenes = $projects->whereNotNull('scene'))
    @php($earlier = $projects->whereNull('scene'))

    <x-page-head :tag="__('site.pages.work.tag') . ' · ' . sprintf('%02d', $projects->count())" :title="__('site.pages.work.title')" :lead="__('site.pages.work.lead')" />

    {{-- Each project in its window, as on the board --}}
    <section class="wrap grid gap-x-8 gap-y-14 pb-24 lg:grid-cols-2" data-stagger>
        @foreach ($scenes as $project)
            <a href="{{ route('projects.show', ['project' => $project->slug()]) }}" class="proj" data-cursor="{{ __('site.work.open') }}" data-reveal>
                @if ($project->hasCover())
                    <x-window :src="$project->coverImage()" :alt="$project->localized('title')" :name="$project->name()" />
                @endif
                <span class="block">
                    <span class="uml__stereo !justify-start"><span>«{{ __('site.pages.work.kinds.' . $project->kind) }}»</span><span class="num">{{ $project->year }}</span><span>{{ $project->localized('sector') }}</span></span>
                    <span class="proj__name block">{{ $project->name() }}</span>
                    <span class="uml__line block">{{ __('site.worlds.' . $project->scene . '.line') }}</span>
                </span>
            </a>
        @endforeach
    </section>

    @if ($earlier->isNotEmpty())
        <section class="sec !pt-0">
            <h2 class="d-2 mb-10" data-words>{{ __('site.pages.work.earlier') }}</h2>
            <div class="rows">
                @foreach ($earlier as $project)
                    <a href="{{ route('projects.show', ['project' => $project->slug()]) }}" class="row" data-reveal>
                        <span class="row__n">{{ $project->year }}</span>
                        <span class="row__title">{{ $project->name() }}</span>
                        <span class="row__sub">{{ $project->localized('summary') }}</span>
                        <span class="label">{{ $project->localized('sector') }}</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</x-layout>
