@props([
    'width' => 'default',
    'bg' => 'surface',
    'spacing' => 'default',
    'tag' => 'section',
    'aurora' => null,
])

@php
    // One spacing system for the whole site. Edit the scales here, never inline.
    $widths = [
        'prose' => 'max-w-2xl',
        'narrow' => 'max-w-3xl',
        'default' => 'max-w-6xl',
        'wide' => 'max-w-7xl',
        'full' => 'max-w-none',
    ];

    // Translucent so the fixed space gradient + starfield show through, the same
    // way the home page reads through its transparent sections.
    $backgrounds = [
        'surface' => '',
        'muted' => 'sec-muted',
        'dark' => 'sec-dark text-ink-on-dark',
        'none' => '',
    ];

    $spacings = [
        'default' => 'py-20 sm:py-28',
        'tight' => 'py-14 sm:py-20',
        'hero' => 'py-24 sm:py-32',
        'flush' => 'py-0',
    ];

    // Aurora is OFF by default now: the global space backdrop + starfield are the
    // ambient layer. Opt a section back in with :aurora="true" if ever needed.
    $showAurora = $aurora === null ? false : (bool) $aurora;

    $widthClass = $widths[$width] ?? $widths['default'];
    $bgClass = $backgrounds[$bg] ?? $backgrounds['surface'];
    $spacingClass = $spacings[$spacing] ?? $spacings['default'];
@endphp

<{{ $tag }} {{ $attributes->class([
    'relative',
    $bgClass,
    'isolate overflow-hidden' => $showAurora,
]) }}>
    @if ($showAurora)
        <div class="aurora" aria-hidden="true">
            <div class="aurora-blob aurora-blob-1"></div>
            <div class="aurora-blob aurora-blob-2"></div>
            <div class="aurora-blob aurora-blob-3"></div>
        </div>
    @endif

    <div class="relative mx-auto w-full px-5 sm:px-6 lg:px-8 {{ $widthClass }} {{ $spacingClass }}">
        {{ $slot }}
    </div>
</{{ $tag }}>
