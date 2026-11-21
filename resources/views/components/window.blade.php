@props(['src', 'alt' => '', 'name' => '', 'small' => null, 'sizes' => '(min-width: 64rem) 50vw, 100vw'])
{{-- A browser window as drawn on the board: cut corners, a bar with three rings. --}}
<figure {{ $attributes->class('win framed') }}>
    <div class="framed__in">
        <div class="win__bar" aria-hidden="true"><i></i><i></i><i></i><b>{{ $name }}</b></div>
        <img src="{{ $src }}" @if ($small && $small !== $src) srcset="{{ $small }} 1600w, {{ $src }} 3200w" sizes="{{ $sizes }}" @endif alt="{{ $alt }}" width="3200" height="2000" loading="lazy" decoding="async">
    </div>
</figure>
