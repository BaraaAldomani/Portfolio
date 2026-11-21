{{-- ═════════ What I build, with the prices on the table ═════════ --}}
@php($offers = config('pricing.offers'))
<section class="sec" id="offers" @if ($j ?? false) data-j @endif>
    <header class="mb-14 grid gap-6 lg:mb-20 lg:grid-cols-12">
        <p class="label lg:col-span-3" data-reveal><span class="num">(03)</span> {{ __('site.offers.tag') }}</p>
        <h2 class="d-2 lg:col-span-9" data-words>{{ accent(__('site.offers.title')) }}</h2>
    </header>

    <div class="rows">
        @foreach ($offers as $key => $price)
            <a href="{{ service_url($key) }}" class="row" data-reveal>
                <span class="row__n">{{ sprintf('%02d', $loop->iteration) }}</span>
                <span>
                    <span class="row__title block">{{ __("site.offers.items.$key.title") }}</span>
                    <span class="row__sub mt-2 block">{{ __("site.offers.items.$key.blurb") }}</span>
                </span>
                <span class="label">{{ __('site.offers.duration') }}: {{ __("site.offers.items.$key.time") }}</span>
                <span class="row__price"><span class="label">{{ __('site.offers.from') }}</span> <span class="num">{{ number_format($price['from']) }}</span> {{ __('site.offers.currency') }}</span>
            </a>
        @endforeach
    </div>

    <div class="mt-12 flex flex-wrap items-center justify-between gap-8" data-reveal>
        <p class="max-w-xl text-sm text-muted">{{ __('site.offers.note') }}</p>
        <a href="{{ route('services') }}#estimate" data-track="cta_estimate" data-track-from="offers" class="rbtn" data-magnetic><span>{{ __('site.hero.cta_estimate') }}</span></a>
    </div>
</section>
