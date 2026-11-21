{{-- One stop on the line. The drawing is made on the canvas by board.js; this
     is what it says: the stage, one sentence, and what the client receives.
     Plain words on purpose: a client should not need a legend. --}}
<section class="j-station j-station--step" data-j data-station="{{ $key }}">
    <div class="j-station__pin">
        <article class="uml framed stepcard" data-card>
            <header class="uml__head">
                <p class="uml__stereo">
                    @if ($n === 0)
                        <span>{{ __('site.b.journey.start') }}</span>
                    @else
                        <span>{{ __('site.b.journey.step') }}</span>
                        <span class="num" dir="ltr">{{ $n }} / {{ $total }}</span>
                    @endif
                </p>
                <h2 class="uml__name">{{ $step['title'] }}</h2>
                <p class="stepcard__text">{{ $step['text'] }}</p>
            </header>
            @isset($step['out'])
                <p class="uml__part stepcard__out"><span>{{ __('site.b.journey.get') }}</span>{{ $step['out'] }}</p>
            @endisset
        </article>
    </div>
</section>
