@props(['href', 'variant' => 'primary'])
@php($styles = [
    'primary' => 'js-magnetic bg-primary text-white shadow-lg shadow-primary-600/25 hover:bg-primary-700',
    'ghost' => 'border border-line-on-dark text-ink-on-dark hover:bg-white/10',
    'light' => 'border border-line text-ink hover:border-primary-300 hover:text-primary-700',
])
<a href="{{ $href }}"
   {{ $attributes->class(['btn-press inline-flex items-center justify-center gap-2 rounded-full px-7 py-3.5 text-sm font-bold transition-colors', $styles[$variant]]) }}>
    {{ $slot }}
</a>
