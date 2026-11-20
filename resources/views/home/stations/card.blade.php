{{-- One stop on the board. The window is drawn on the canvas by board.js; this
     is the card that describes it, shaped like a UML class: a name, its
     attributes, and what it does. The <img> shows only when there is no canvas. --}}
@php($scene = $project->scene)
<section class="j-station j-station--{{ $scene }}" data-j data-station="{{ $scene }}" @if ($scene === 'rouaa') data-retheme @endif>
    <div class="j-station__pin">
        <article class="uml framed" data-card>
            <header class="uml__head">
                <p class="uml__stereo">
                    <span>«{{ __('site.pages.work.kinds.' . $project->kind) }}»</span>
                    <span class="num" dir="ltr">{{ sprintf('%02d', $n) }} / {{ sprintf('%02d', $total) }}</span>
                </p>
                <h2 class="uml__name">{{ $project->name() }}</h2>
                <p class="uml__line">{{ __('site.worlds.' . $scene . '.line') }}</p>
            </header>

            <dl class="uml__part uml__attrs">
                <div><dt>{{ __('site.pages.project.sector') }}</dt><dd>{{ $project->localized('sector') }}</dd></div>
                @if ($project->localized('role'))
                    <div><dt>{{ __('site.pages.project.role') }}</dt><dd>{{ $project->localized('role') }}</dd></div>
                @endif
            </dl>

            @if ($scene === 'rouaa')
                <div class="uml__part">
                    <p class="uml__hint">{{ __('site.worlds.rouaa.hint') }}</p>
                    <div class="swatches mt-2" role="group" aria-label="{{ __('site.worlds.rouaa.hint') }}">
                        @foreach ([['#24abe3', 0], ['#2fd39a', -42], ['#9b83ff', 58], ['#ff6f96', 142], ['#f0a23a', 196]] as [$tint, $hue])
                            <button type="button" class="swatch" style="--c: {{ $tint }}" data-tint="{{ $tint }}" data-hue="{{ $hue }}"
                                    aria-pressed="{{ $loop->first ? 'true' : 'false' }}" aria-label="{{ $tint }}"></button>
                        @endforeach
                    </div>
                </div>
            @else
                <ul class="uml__part uml__ops">
                    @foreach (array_slice($project->highlights(), 0, 3) as $highlight)
                        <li>{{ $highlight }}</li>
                    @endforeach
                </ul>
            @endif

            <footer class="uml__part uml__foot">
                <x-scene-links :project="$project" />
            </footer>
        </article>

        @if ($project->hasCover())
            <img class="j-station__fallback" src="{{ $project->coverImage() }}" alt="{{ $project->localized('title') }}" width="3200" height="2000" loading="lazy" decoding="async">
        @endif
    </div>
</section>
