<x-layout :meta="$meta">
    <x-section spacing="hero">
        <div class="mx-auto flex max-w-2xl flex-col items-center text-center">
            <span class="reveal inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-primary-50 text-primary-700" aria-hidden="true">
                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
            </span>
            <h1 class="reveal mt-6 text-3xl font-extrabold text-ink sm:text-4xl">{{ $heading }}</h1>
            <p class="reveal mt-4 text-lg text-muted">{{ __('common.coming_soon') }}</p>
            <x-cta-button :href="route('home')" variant="light" class="reveal mt-9">{{ __('common.back_home') }}</x-cta-button>
        </div>
    </x-section>
</x-layout>
