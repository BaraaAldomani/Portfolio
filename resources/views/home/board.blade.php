{{-- ═════════ THE BOARD ═════════
     The home page as one flat drawing, and a camera that travels it
     (resources/js/motion/board.js). The drawing speaks the logo's language
     (one weight of line, corners cut at 45°, octagons, dashed paths, rings, a
     dot, an arrowhead) with shapes borrowed from UML.

       hero       an idea is typed, becomes a diagram, becomes a real product
       roadmap    four steps down a line, each answering a founder's question
       projects   four windows; each is a drawing until the pen arrives
       the rest   read while the camera pulls back over the whole sheet

     The canvas stays pinned; every [data-j] section scrolls over it. The
     words on the sheet come from the lang files through data-board; the same
     words are also in the HTML below, so the page reads without the canvas. --}}
@php($order = ['sajjah', 'shattib', 'rouaa', 'nour'])
@php($stations = collect($order)->map(fn ($scene) => $projects->firstWhere('scene', $scene))->filter()->values())
@php($archive = $projects->whereNull('scene'))
@php($byScene = $projects->whereNotNull('scene')->keyBy('scene'))
@php($board = [
    'labels' => __('site.b.labels'),
    'questions' => array_values((array) __('site.b.questions')),
    'steps' => collect((array) __('site.process.steps'))->map(fn ($step) => ['title' => $step['title'], 'text' => $step['text']])->values(),
    // The hero loop: a founder's sentence, the parts it breaks into, and the real project it became.
    'ideas' => collect((array) __('site.b.ideas'))->filter(fn ($idea) => $byScene->has($idea['scene']) && $byScene[$idea['scene']]->hasCover())->map(fn ($idea) => [
        'say' => $idea['say'],
        'steps' => $idea['steps'],
        'name' => $byScene[$idea['scene']]->name(),
        'image' => $byScene[$idea['scene']]->coverImage(),
    ])->values(),
    'plots' => $stations->map(fn ($p) => [
        'scene' => $p->scene,
        'name' => $p->name(),
        'image' => $p->hasCover() ? $p->coverImage() : null,
        'phone' => $p->mobileImage(),
        'plan' => $p->scene === 'shattib' ? asset('images/projects/shattib-plan.webp') : null,
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

    {{-- Two statements, pinned low on the screen while the line is drawn above them. --}}
    <section class="j-wake j-wake--pinned" data-j data-j-pin>
        <div class="j-wake__pin">
            <p class="statement">{{ __('site.b.a') }}</p>
            <ul class="sr-only">
                @foreach ((array) __('site.b.questions') as $question)
                    <li>{{ $question }}</li>
                @endforeach
            </ul>
            <p class="statement">{{ __('site.b.b') }}</p>
        </div>
    </section>

    @foreach ($stations as $project)
        @include('home.stations.card', ['project' => $project, 'n' => $loop->iteration, 'total' => $stations->count()])
    @endforeach

    @if ($archive->isNotEmpty())
        <section class="sec" data-j>
            <div class="mb-12 flex flex-wrap items-end justify-between gap-6">
                <h2 class="d-2" data-words>{{ __('site.worlds.archive.title') }}</h2>
                <a href="{{ route('projects.index') }}" class="ulink" data-reveal>
                    {{ __('site.work.all') }}
                    <svg class="arrow-fwd" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                </a>
            </div>
            <div class="rows">
                @foreach ($archive as $project)
                    <a href="{{ route('projects.show', ['project' => $project->slug()]) }}" class="row" data-reveal>
                        <span class="row__n">{{ $project->year }}</span>
                        <span class="row__title">{{ $project->name() }}</span>
                        <span class="row__sub">{{ $project->localized('stat') }}</span>
                        <span class="label">{{ $project->localized('sector') }}</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    @include('home.offers', ['j' => true])
    @include('home.method', ['j' => true])
    @include('home.faq', ['j' => true])
</div>
