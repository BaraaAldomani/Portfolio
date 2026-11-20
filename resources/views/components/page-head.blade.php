@props(['tag', 'title', 'lead' => null])
<header {{ $attributes->class('page-head') }}>
    <p class="label" data-reveal>{{ $tag }}</p>
    <h1 class="d-1 mt-5" data-words>{{ accent($title) }}</h1>
    @if ($lead)
        <p class="lede mt-8 max-w-2xl text-muted" data-reveal>{{ $lead }}</p>
    @endif
    {{ $slot }}
    <x-trace />
</header>
