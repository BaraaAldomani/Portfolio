<x-layout :meta="$meta" page="service">
    @php
        $offers = config('pricing.offers');
        $price = $offers[$key];
        $position = array_search($key, array_keys($offers), true) + 1;
        $ui = fn (string $word): string => __("service_pages.ui.$word");
        $offer = fn (string $which, string $field) => __("site.offers.items.$which.$field");
        $ask = whatsapp_url(__('site.pages.services.ask_prefix') . ' ' . $offer($key, 'title'));
        $covers = $projects->filter(fn ($project) => $project->hasCover())->values();
        $earlier = $projects->reject(fn ($project) => $project->hasCover())->values();
        $reviewed = config('pricing.reviewed') ? \Illuminate\Support\Carbon::parse(config('pricing.reviewed')) : null;
    @endphp

    <article>
        {{-- ═════════ Head: the service, in the words people search for ═════════ --}}
        <header class="page-head page-head--long">
            <a href="{{ route('services') }}" class="ulink self-start text-sm" data-reveal>
                <svg class="arrow-fwd rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                {{ $ui('back') }}
            </a>

            <p class="label mt-12" data-reveal>«{{ __('site.offers.kind') }}» {{ $offer($key, 'title') }}</p>
            <h1 class="d-1 mt-3" data-words>{{ accent($c['title']) }}</h1>
            <p class="lede mt-8 max-w-2xl text-muted" data-reveal>{{ $c['lead'] }}</p>

            <div class="mt-10 flex flex-wrap items-center gap-x-5 gap-y-4" data-reveal>
                <a href="{{ $ask }}" target="_blank" rel="noopener" data-track="whatsapp" data-track-from="service-{{ $key }}" class="cta cta--wa">
                    <x-icon.whatsapp class="h-5 w-5" />{{ $ui('ask') }}
                </a>
                <a href="#estimate" data-track="cta_estimate" data-track-from="service-{{ $key }}" class="cta cta--line">{{ $ui('estimate') }}</a>
            </div>

            <x-trace />
        </header>

        {{-- ═════════ The short answer, and the service described like a class ═════════ --}}
        <section class="wrap grid gap-x-10 gap-y-10 pb-8 lg:grid-cols-12">
            <div class="lg:col-span-7">
                <h2 class="label font-sans font-normal" data-reveal>{{ $ui('answer') }}</h2>
                <p class="statement mt-4 !text-[clamp(1.3rem,2.2vw,2.3rem)]" data-reveal>{{ $c['answer'] }}</p>

                {{-- Who does the work and from where, and how fresh the price is. --}}
                <p class="mt-8 max-w-2xl text-muted" data-reveal>
                    {!! str_replace(':name', '<a href="' . e(route('about')) . '" class="ulink text-ink">' . e(__('common.brand')) . '</a>', e($ui('by'))) !!}
                    @if ($reviewed)
                        <span class="whitespace-nowrap">{{ __('service_pages.ui.updated', ['date' => $reviewed->locale(app()->getLocale())->translatedFormat('F Y')]) }}<time datetime="{{ $reviewed->toDateString() }}" class="sr-only">{{ $reviewed->toDateString() }}</time></span>
                    @endif
                </p>
            </div>

            <aside class="uml framed self-start lg:col-span-4 lg:col-start-9" data-reveal>
                <div class="uml__head">
                    <p class="uml__stereo">
                        <span>«{{ __('site.offers.kind') }}»</span>
                        <span class="num" dir="ltr">{{ sprintf('%02d', $position) }} / {{ sprintf('%02d', count($offers)) }}</span>
                    </p>
                    <p class="uml__name uml__name--sm">{{ $offer($key, 'title') }}</p>
                </div>
                <dl class="uml__part uml__attrs uml__attrs--wide">
                    <div><dt>{{ __('site.offers.from') }}</dt><dd class="uml__price"><span class="num">{{ number_format($price['from']) }}</span> {{ __('site.offers.currency') }}</dd></div>
                    <div><dt>{{ __('site.offers.duration') }}</dt><dd>{{ $offer($key, 'time') }}</dd></div>
                </dl>
                <ul class="uml__part uml__ops" aria-label="{{ __('site.offers.includes') }}">
                    @foreach ((array) $offer($key, 'includes') as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
                <p class="uml__part uml__hint">{{ __('site.offers.note') }}</p>
            </aside>
        </section>

        {{-- ═════════ Who it is for, and who it is not ═════════ --}}
        <section class="sec">
            <div class="grid gap-x-8 gap-y-10 lg:grid-cols-12">
                <h2 class="d-2 lg:col-span-5" data-words>{{ accent($c['fit_title']) }}</h2>
                <div class="lg:col-span-7">
                    <ul class="rows">
                        @foreach ($c['fit'] as $line)
                            <li class="row row--2" data-reveal>
                                <span class="row__n" dir="ltr">{{ sprintf('%02d', $loop->iteration) }}</span>
                                <p class="lede">{{ $line }}</p>
                            </li>
                        @endforeach
                    </ul>
                    @if (! empty($c['not_fit']['text']))
                        <p class="mt-8 max-w-2xl text-muted" data-reveal>
                            {{ $c['not_fit']['text'] }}
                            @if (! empty($c['not_fit']['to']))
                                <a href="{{ service_url($c['not_fit']['to']) }}" class="ulink text-ink">{{ $offer($c['not_fit']['to'], 'title') }}</a>
                            @endif
                        </p>
                    @endif
                </div>
            </div>
        </section>

        {{-- ═════════ What is handed over ═════════ --}}
        <section class="sec !pt-0">
            <h2 class="d-2 mb-10 lg:mb-14" data-words>{{ accent($c['gets_title']) }}</h2>
            <div class="rows">
                @foreach ($c['gets'] as $item)
                    <div class="row row--3 row--hover" data-reveal>
                        <span class="row__n" dir="ltr">{{ sprintf('%02d', $loop->iteration) }}</span>
                        <h3 class="row__title !text-[clamp(1.35rem,2.5vw,2.5rem)]">{{ $item['t'] }}</h3>
                        <p class="row__sub">{{ $item['d'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ═════════ Proof: real work of this kind ═════════ --}}
        @if ($projects->isNotEmpty())
            <section class="sec !pt-0">
                <header class="mb-12 grid gap-6 lg:mb-16 lg:grid-cols-12">
                    <h2 class="d-2 lg:col-span-6" data-words>{{ accent($c['proof_title']) }}</h2>
                    {{-- Says exactly what the work below is, so nothing is implied that it does not show. --}}
                    <p class="lede self-end text-muted lg:col-span-5 lg:col-start-8" data-reveal>{{ $c['proof_lead'] }} {{ $ui('open') }}</p>
                </header>

                @if ($covers->isNotEmpty())
                    <div @class(['grid gap-x-8 gap-y-14', 'lg:grid-cols-2' => $covers->count() > 1, 'lg:max-w-[62%]' => $covers->count() === 1]) data-stagger>
                        @foreach ($covers as $project)
                            <a href="{{ route('projects.show', ['project' => $project->slug()]) }}" class="proj" data-cursor="{{ __('site.work.open') }}" data-reveal>
                                <x-window :src="$project->coverImage()" :small="$project->coverSmall()" :alt="$project->localized('title')" :name="$project->name()" />
                                <span class="block">
                                    <span class="uml__stereo !justify-start"><span>«{{ __('site.pages.work.kinds.' . $project->kind) }}»</span><span class="num">{{ $project->year }}</span><span>{{ $project->localized('sector') }}</span></span>
                                    <span class="proj__name block">{{ $project->name() }}</span>
                                    <span class="uml__line block">{{ $project->localized('summary') }}</span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                @endif

                @if ($earlier->isNotEmpty())
                    <div @class(['rows', 'mt-16' => $covers->isNotEmpty()])>
                        @foreach ($earlier as $project)
                            <a href="{{ route('projects.show', ['project' => $project->slug()]) }}" class="row" data-reveal>
                                <span class="row__n">{{ $project->year }}</span>
                                <span class="row__title !text-[clamp(1.5rem,3.2vw,3.2rem)]">{{ $project->name() }}</span>
                                <span class="row__sub">{{ $project->localized('summary') }}</span>
                                <span class="label">{{ $project->localized('sector') }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </section>
        @elseif ($key === 'ai')
            {{-- No client project to show yet: the proof is how the work itself is done. --}}
            @include('home.method')
        @endif

        {{-- ═════════ The calculator, already set to this service ═════════ --}}
        @include('partials.estimator', ['preset' => $key, 'title' => $c['estimate_title']])

        @include('home.process')

        {{-- ═════════ Questions about this service (also marked up as FAQPage) ═════════ --}}
        <section class="sec">
            <div class="grid gap-x-8 gap-y-12 lg:grid-cols-12">
                <header class="lg:col-span-5">
                    <div class="lg:sticky lg:top-[calc(var(--nav-h)+2rem)]">
                        <h2 class="d-2" data-words>{{ accent($c['faq_title']) }}</h2>
                    </div>
                </header>

                <div class="lg:col-span-7">
                    @foreach ($c['faq'] as $item)
                        <details class="faq" data-reveal @if ($loop->first) open @endif>
                            <summary><h3>{{ $item['q'] }}</h3><span class="faq__x" aria-hidden="true"></span></summary>
                            <p>{{ $item['a'] }}</p>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ═════════ The other five ═════════ --}}
        <section class="sec !pt-0">
            <h2 class="d-2 mb-10" data-words>{{ accent($ui('others')) }}</h2>
            <div class="rows">
                @foreach ($offers as $other => $otherPrice)
                    @continue($other === $key)
                    <a href="{{ service_url($other) }}" class="row" data-reveal>
                        <span class="row__n" dir="ltr">{{ sprintf('%02d', $loop->iteration) }}</span>
                        <span>
                            <span class="row__title block !text-[clamp(1.5rem,3.2vw,3.2rem)]">{{ $offer($other, 'title') }}</span>
                            <span class="row__sub mt-2 block">{{ $offer($other, 'blurb') }}</span>
                        </span>
                        <span class="label">{{ __('site.offers.duration') }}: {{ $offer($other, 'time') }}</span>
                        <span class="row__price"><span class="label">{{ __('site.offers.from') }}</span> <span class="num">{{ number_format($otherPrice['from']) }}</span> {{ __('site.offers.currency') }}</span>
                    </a>
                @endforeach
            </div>
        </section>
    </article>
</x-layout>
