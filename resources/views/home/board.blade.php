{{-- ═════════ THE BOARD ═════════
     The home page as one flat drawing, and a camera that travels it
     (resources/js/motion/board.js). The drawing speaks the logo's language:
     one weight of line, corners cut at 45°, octagons, dashed paths, rings, a
     dot, an arrowhead.

       hero       an idea is typed, becomes a diagram, becomes a real product
       the line   one line tells how the work goes: a tangle at the idea, in
                  order at the analysis, drawing screens at the design, filled
                  in and checked at development, a rocket at the launch
       landing    the rocket flies to the WhatsApp button
       the rest   read while the camera pulls back over the whole sheet

     Nothing here needs explaining to a client: each stop is a name, one
     sentence and what they receive. Projects are on the Work page; the only
     real screenshots on this page are the hero's.

     The canvas stays pinned; every [data-j] section scrolls over it. The
     words on the sheet come from the lang files through data-board; the same
     words are also in the HTML below, so the page reads without the canvas. --}}
@php($byScene = $projects->whereNotNull('scene')->keyBy('scene'))
@php($steps = (array) __('site.b.journey.steps'))
@php($board = [
    'labels' => __('site.b.labels'),
    'questions' => array_values((array) __('site.b.questions')),
    'rows' => array_values((array) __('site.b.journey.rows')),
    // The stops of the line, in order: what stands above each drawing.
    'steps' => collect($steps)->pluck('title')->values(),
    // The hero loop: a founder's sentence, the parts it breaks into, and the real project it became.
    'ideas' => collect((array) __('site.b.ideas'))->filter(fn ($idea) => $byScene->has($idea['scene']) && $byScene[$idea['scene']]->hasCover())->map(fn ($idea) => [
        'say' => $idea['say'],
        'steps' => $idea['steps'],
        'name' => $byScene[$idea['scene']]->name(),
        'image' => $byScene[$idea['scene']]->coverSmall(),
    ])->values(),
])

<div class="journey board" data-board='@json($board, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_APOS)'>
    <div class="journey__stage" aria-hidden="true">
        <canvas></canvas>
        <div class="board__shade"></div>
        <p class="board__hud num" data-board-hud></p>
    </div>

    {{-- Hero: the words on the reading side; beside them, on the canvas, an
         idea being typed and turning into a product. --}}
    <section class="intro" data-j>
        <div class="intro__copy">
            <p class="label intro__status" data-reveal><span class="pulse me-2"></span>{{ __('site.hero.status') }} <span class="intro__more"><span class="intro__sep">/</span> {{ __('site.hero.role') }} <span class="intro__sep">/</span> {{ __('site.hero.place') }} <span class="num" data-clock></span></span></p>

            <h1 class="intro__title">
                <span class="block" data-words>{{ __('site.b.hero.l1') }}</span>
                <span class="block" data-words style="--d:.1s"><em class="alt">{{ __('site.b.hero.alt') }}</em> {{ __('site.b.hero.l2') }}</span>
                <span class="block" data-words style="--d:.2s">{{ __('site.b.hero.l3') }}</span>
            </h1>

            <p class="lede intro__lead" data-reveal style="--d:.45s">{{ __('site.b.hero.lead') }}</p>

            <div class="intro__actions" data-reveal style="--d:.55s">
                <a href="{{ route('services') }}#estimate" data-track="cta_estimate" data-track-from="hero" class="cta">{{ __('site.hero.cta_estimate') }}</a>
                <a href="{{ whatsapp_url() }}" target="_blank" rel="noopener" data-track="whatsapp" data-track-from="hero" class="ulink">
                    <x-icon.whatsapp />{{ __('common.whatsapp') }}
                </a>
            </div>
        </div>

        {{-- What the canvas plays, as text. --}}
        <ul class="sr-only">
            @foreach ($board['ideas'] as $idea)
                <li>{{ $idea['say'] }}: {{ implode('، ', $idea['steps']) }} ← {{ $idea['name'] }}</li>
            @endforeach
        </ul>

        <p class="hero__scroll label" aria-hidden="true">{{ __('site.b.hero.scroll') }}</p>
    </section>

    {{-- The line: five stops. The drawing is on the canvas; this is what each
         stop says. --}}
    @foreach ($steps as $key => $step)
        @include('home.stations.step', ['key' => $key, 'step' => $step, 'n' => $loop->index, 'total' => count($steps) - 1])
    @endforeach

    {{-- Where the rocket lands: the first step, which is free. --}}
    <section class="landing" data-j data-landing>
        <div class="landing__in">
            <h2 class="d-2" data-words>{{ accent(__('site.b.journey.landing.title')) }}</h2>
            <p class="lede mt-6 max-w-xl text-muted" data-reveal>{{ __('site.b.journey.landing.lead') }}</p>
            <div class="landing__actions" data-reveal>
                <span class="landing__pad" data-rocket-pad>
                    <a href="{{ whatsapp_url() }}" target="_blank" rel="noopener" data-track="whatsapp" data-track-from="journey" class="rbtn rbtn--wa" data-rocket-target data-magnetic>
                        <span><x-icon.whatsapp class="mx-auto mb-1 h-6 w-6" />{{ __('site.cta.whatsapp') }}</span>
                    </a>
                </span>
                <a href="{{ route('services') }}#estimate" data-track="cta_estimate" data-track-from="journey" class="cta cta--line">{{ __('site.hero.cta_estimate') }}</a>
                <a href="{{ route('projects.index') }}" class="ulink">
                    {{ __('site.b.journey.landing.work') }}
                    <svg class="arrow-fwd" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                </a>
            </div>
        </div>
    </section>

    @include('home.offers', ['j' => true])
    @include('home.method', ['j' => true])
    @include('home.faq', ['j' => true])
</div>
