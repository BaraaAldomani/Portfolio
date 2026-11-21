<x-layout :meta="$meta" page="services">
    @php($offers = config('pricing.offers'))

    <x-page-head :tag="__('site.pages.services.tag')" :title="__('site.pages.services.title')" :lead="__('site.pages.services.lead')">
        <div class="mt-10" data-reveal>
            <a href="#estimate" class="cta">{{ __('site.hero.cta_estimate') }}</a>
        </div>
    </x-page-head>

    {{-- ═════════ The six offers, each described like a class: what it is,
         what it costs, what it does ═════════ --}}
    <section class="sec !pt-0">
        <div class="cards" data-stagger>
            @foreach ($offers as $key => $price)
                <article id="{{ $key }}" class="uml framed scroll-mt-24" data-reveal>
                    <header class="uml__head">
                        <p class="uml__stereo">
                            <span>«{{ __('site.offers.kind') }}»</span>
                            <span class="num" dir="ltr">{{ sprintf('%02d', $loop->iteration) }} / {{ sprintf('%02d', count($offers)) }}</span>
                        </p>
                        <h2 class="uml__name uml__name--sm"><a href="{{ service_url($key) }}">{{ __("site.offers.items.$key.title") }}</a></h2>
                        <p class="uml__line">{{ __("site.offers.items.$key.blurb") }}</p>
                    </header>
                    <dl class="uml__part uml__attrs uml__attrs--wide">
                        <div><dt>{{ __('site.offers.from') }}</dt><dd class="uml__price"><span class="num">{{ number_format($price['from']) }}</span> {{ __('site.offers.currency') }}</dd></div>
                        <div><dt>{{ __('site.offers.duration') }}</dt><dd>{{ __("site.offers.items.$key.time") }}</dd></div>
                    </dl>
                    <ul class="uml__part uml__ops">
                        @foreach ((array) __("site.offers.items.$key.includes") as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                    <footer class="uml__part uml__foot flex flex-wrap items-center justify-between gap-x-6 gap-y-2">
                        <a href="{{ service_url($key) }}" class="ulink" aria-label="{{ __('service_pages.ui.details') }}: {{ __("site.offers.items.$key.title") }}">{{ __('service_pages.ui.details') }}</a>
                        <a href="{{ whatsapp_url(__('site.pages.services.ask_prefix') . ' ' . __("site.offers.items.$key.title")) }}" target="_blank" rel="noopener"
                           data-track="whatsapp" data-track-from="offer-{{ $key }}" class="ulink">{{ __('site.offers.ask') }}</a>
                    </footer>
                </article>
            @endforeach
        </div>
        <p class="mt-8 max-w-xl text-sm text-muted">{{ __('site.offers.note') }}</p>
    </section>

    @include('partials.estimator')
    @include('home.process')
    @include('home.faq')
</x-layout>
