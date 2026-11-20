{{-- ═════════ How I work: with AI agents, and in four steps ═════════ --}}
<section class="sec" @if ($j ?? false) data-j @endif>
    <header class="mb-14 grid gap-6 lg:mb-20 lg:grid-cols-12">
        <p class="label lg:col-span-3" data-reveal><span class="num">(04)</span> {{ __('site.ai.tag') }}</p>
        <div class="lg:col-span-9">
            <h2 class="d-2" data-words>{{ accent(__('site.ai.title')) }}</h2>
            <p class="lede mt-8 max-w-2xl text-muted" data-reveal>{{ __('site.ai.lead') }}</p>
        </div>
    </header>

    <div class="rows">
        @foreach ((array) __('site.ai.points') as $point)
            <div class="row row--3 row--hover" data-reveal>
                <span class="row__n">{{ sprintf('%02d', $loop->iteration) }}</span>
                <span class="row__title">{{ $point['title'] }}</span>
                <span class="row__sub">{{ $point['text'] }}</span>
            </div>
        @endforeach
    </div>
</section>
