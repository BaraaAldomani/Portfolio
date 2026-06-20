@props(['eyebrow', 'title', 'lead' => null])
<div {{ $attributes->class(['reveal max-w-2xl']) }}>
    <p class="text-sm font-bold tracking-wide text-primary-700">{{ $eyebrow }}</p>
    <h2 class="mt-2 text-2xl font-extrabold text-ink sm:text-3xl">{{ $title }}</h2>
    @if ($lead)
        <p class="mt-3 leading-relaxed text-muted">{{ $lead }}</p>
    @endif
</div>
