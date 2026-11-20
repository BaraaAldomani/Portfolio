{{-- ═════════ Project calculator ═════════
     Reads config/pricing.php (single source of prices). Works as plain form
     controls; services.js adds the live total and the WhatsApp hand-off. --}}
@php
    $pricing = config('pricing');
    $estimatorConfig = [
        'offers' => $pricing['offers'],
        'extras' => $pricing['extras'],
        'pace' => $pricing['pace'],
        'spread' => $pricing['spread'],
        'whatsapp' => whatsapp_url(''),
        'labels' => [
            'offers' => collect($pricing['offers'])->keys()->mapWithKeys(fn ($k) => [$k => __("site.offers.items.$k.title")]),
            'extras' => __('site.estimate.extras'),
            'pace' => __('site.estimate.pace'),
            'intro' => __('site.estimate.message_intro'),
            'type' => __('site.estimate.message_type'),
            'extrasLabel' => __('site.estimate.message_extras'),
            'paceLabel' => __('site.estimate.message_pace'),
            'range' => __('site.estimate.message_range'),
            'none' => __('site.estimate.none'),
            'to' => __('site.estimate.to'),
            'currency' => __('site.offers.currency'),
            'weeks' => __('site.estimate.weeks'),
        ],
    ];
@endphp
<section id="estimate" class="sec scroll-mt-10">
    <header class="mb-14 grid gap-6 lg:mb-20 lg:grid-cols-12">
        <p class="label lg:col-span-3" data-reveal>{{ __('site.estimate.tag') }}</p>
        <div class="lg:col-span-9">
            <h2 class="d-2" data-words>{{ accent(__('site.estimate.title')) }}</h2>
            <p class="lede mt-6 max-w-2xl text-muted" data-reveal>{{ __('site.estimate.lead') }}</p>
        </div>
    </header>

    <form class="grid gap-x-12 gap-y-12 lg:grid-cols-12" data-estimator data-config='@json($estimatorConfig, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS)'>
        <div class="space-y-12 lg:col-span-7">
            <fieldset data-reveal>
                <legend class="d-3">{{ __('site.estimate.step_type') }}</legend>
                <div class="mt-5 flex flex-wrap gap-3">
                    @foreach ($pricing['offers'] as $key => $price)
                        <label class="opt">
                            <input type="radio" name="type" value="{{ $key }}" @checked($loop->first)>
                            <span>{{ __("site.offers.items.$key.title") }}</span>
                        </label>
                    @endforeach
                </div>
            </fieldset>

            <fieldset data-reveal>
                <legend class="d-3">{{ __('site.estimate.step_extras') }}</legend>
                <div class="mt-5 flex flex-wrap gap-3">
                    @foreach ($pricing['extras'] as $key => $extra)
                        <label class="opt">
                            <input type="checkbox" name="extras" value="{{ $key }}">
                            <span>{{ __("site.estimate.extras.$key") }}</span>
                        </label>
                    @endforeach
                </div>
            </fieldset>

            <fieldset data-reveal>
                <legend class="d-3">{{ __('site.estimate.step_pace') }}</legend>
                <div class="mt-5 flex flex-wrap gap-3">
                    @foreach ($pricing['pace'] as $key => $pace)
                        <label class="opt">
                            <input type="radio" name="pace" value="{{ $key }}" @checked($loop->first)>
                            <span>{{ __("site.estimate.pace.$key") }}</span>
                        </label>
                    @endforeach
                </div>
            </fieldset>
        </div>

        <div class="lg:col-span-5">
            <div class="estimate-card framed" data-reveal>
                <p class="label">{{ __('site.estimate.result') }}</p>
                <p class="mt-4 leading-none" aria-live="polite">
                    <span class="num estimate-num" data-est="low">{{ number_format(collect($pricing['offers'])->first()['from']) }}</span>
                    <span class="label mx-2">{{ __('site.estimate.to') }}</span>
                    <span class="num estimate-num" data-est="high">{{ number_format(round(collect($pricing['offers'])->first()['from'] * $pricing['spread'] / 500) * 500) }}</span>
                    <span class="label ms-1">{{ __('site.offers.currency') }}</span>
                </p>

                <p class="label mt-10">{{ __('site.estimate.time') }}</p>
                <p class="d-3 mt-2"><span data-est="weeks">{{ collect($pricing['offers'])->first()['weeks'][0] }} {{ __('site.estimate.to') }} {{ collect($pricing['offers'])->first()['weeks'][1] }}</span> {{ __('site.estimate.weeks') }}</p>

                <button type="submit" class="cta cta--wa mt-10 w-full" data-track="whatsapp" data-track-from="estimator">
                    <x-icon.whatsapp class="h-5 w-5" />{{ __('site.estimate.send') }}
                </button>
                <p class="label mt-4">{{ __('site.estimate.disclaimer') }}</p>
            </div>
        </div>
    </form>
</section>
