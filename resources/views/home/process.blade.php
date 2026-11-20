{{-- ═════════ Four steps, no surprises ═════════ --}}
<div class="mq d-1 mq--outline" data-marquee data-marquee-dir="-1" aria-hidden="true">
    <div class="mq__track">
        @foreach ((array) __('site.process.steps') as $step)
            <span class="mq__item"><span>{{ $step['title'] }}</span><x-star class="mq__star" /></span>
        @endforeach
    </div>
</div>

<section class="sec" @if ($j ?? false) data-j @endif>
    <header class="mb-14 grid gap-6 lg:mb-20 lg:grid-cols-12">
        <p class="label lg:col-span-3" data-reveal><span class="num">(05)</span> {{ __('site.process.tag') }}</p>
        <h2 class="d-2 lg:col-span-9" data-words>{{ accent(__('site.process.title')) }}</h2>
    </header>

    <ol class="steps" data-stagger>
        @foreach ((array) __('site.process.steps') as $step)
            <li class="step" data-reveal>
                <p class="step__n">{{ $loop->iteration }}</p>
                <h3 class="d-3 mt-6">{{ $step['title'] }}</h3>
                <p class="mt-3 text-muted">{{ $step['text'] }}</p>
            </li>
        @endforeach
    </ol>
</section>
