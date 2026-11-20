{{-- ═════════ Questions that come up every time ═════════ --}}
<section class="sec" @if ($j ?? false) data-j @endif>
    <div class="grid gap-x-8 gap-y-12 lg:grid-cols-12">
        <header class="lg:col-span-5">
            <div class="lg:sticky lg:top-[calc(var(--nav-h)+2rem)]">
                <p class="label" data-reveal><span class="num">(06)</span> {{ __('site.faq.tag') }}</p>
                <h2 class="d-2 mt-6" data-words>{{ accent(__('site.faq.title')) }}</h2>
            </div>
        </header>

        <div class="rule lg:col-span-7" style="border-top:0">
            @foreach ((array) __('site.faq.items') as $item)
                <details class="faq" data-reveal @if ($loop->first) open @endif>
                    <summary>{{ $item['q'] }}<span class="faq__x" aria-hidden="true"></span></summary>
                    <p>{{ $item['a'] }}</p>
                </details>
            @endforeach
        </div>
    </div>
</section>
