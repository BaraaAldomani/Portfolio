<x-layout :meta="$meta">
    <x-section bg="muted" spacing="tight" class="border-b border-line">
        <div class="mx-auto max-w-2xl text-center">
            <p class="reveal text-sm font-bold tracking-wide text-primary-700">{{ __('home.projects.eyebrow') }}</p>
            <h1 data-reveal-text class="mt-2 text-3xl font-extrabold text-ink sm:text-4xl">{{ __('home.projects.title') }}</h1>
            <p class="reveal mx-auto mt-4 max-w-2xl text-lg leading-relaxed text-muted">{{ __('home.projects.lead') }}</p>
        </div>
    </x-section>

    <x-section>
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3" data-stagger>
            @foreach ($projects as $project)
                <x-project-card :project="$project" class="reveal" />
            @endforeach
        </div>
    </x-section>
</x-layout>
