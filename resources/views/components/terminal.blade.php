@php($terminal = __('home.terminal'))
<div {{ $attributes->class(['overflow-hidden rounded-2xl border border-line-on-dark bg-surface-dark-soft shadow-2xl']) }}
     dir="ltr" role="img" aria-label="{{ $terminal['aria_label'] }}">
    <div class="flex items-center gap-2 border-b border-line-on-dark px-4 py-3">
        <span class="h-3 w-3 rounded-full bg-danger/80" aria-hidden="true"></span>
        <span class="h-3 w-3 rounded-full bg-accent-600/80" aria-hidden="true"></span>
        <span class="h-3 w-3 rounded-full bg-success/80" aria-hidden="true"></span>
        <span class="ms-2 font-mono text-xs text-muted-on-dark">{{ $terminal['title'] }}</span>
    </div>

    {{-- Lines render server-side (readable without JS); animations.js re-types them. --}}
    <div class="js-terminal terminal-body min-h-72 p-5 text-[13px] leading-7 sm:text-sm"
         data-lines='@json($terminal['lines'])'>
        @foreach ($terminal['lines'] as $line)
            <div class="terminal-line-{{ $line['type'] }}">{{ $line['text'] !== '' ? $line['text'] : ' ' }}</div>
        @endforeach
    </div>
</div>
