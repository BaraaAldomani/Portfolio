{{-- The brand mark, redrawn as clean strokes from public/images/logo.svg (same
     geometry): two stacked octagons forming the "B", the open bowl beside
     them, and the dashed path that ends in a dot. Inherits the text colour;
     the closing dot takes the brand cyan. --}}
<svg {{ $attributes->merge(['viewBox' => '0 0 600 552', 'fill' => 'none', 'aria-hidden' => 'true']) }}>
    <g stroke="currentColor" stroke-width="22" stroke-linejoin="miter">
        <path pathLength="1" d="M110 12h115l75 75v115l-70 68H40V87l70-75Z"/>
        <path pathLength="1" d="M40 270v190l75 75h110l75-75V345l-70-75"/>
        <path pathLength="1" d="M300 87l75-75h115l75 75v115l-60 55"/>
        <path d="M314 270h176l75 85v128" stroke-dasharray="17 12"/>
    </g>
    <path d="M256 270l50-47v94l-50-47Z" fill="currentColor"/>
    <circle cx="34" cy="270" r="25" fill="var(--bg)" stroke="currentColor" stroke-width="20"/>
    <circle cx="565" cy="516" r="34" fill="var(--cyan)"/>
</svg>
