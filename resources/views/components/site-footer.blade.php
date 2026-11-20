@php($footerEmail = setting('contact.email', config('portfolio.email')))
@php($channels = [
    ['label' => __('common.whatsapp'), 'href' => whatsapp_url(), 'track' => 'whatsapp'],
    ['label' => __('common.linkedin'), 'href' => setting('contact.linkedin', config('portfolio.linkedin')), 'track' => 'linkedin'],
    ['label' => __('common.gitlab'), 'href' => setting('contact.gitlab', config('portfolio.gitlab')), 'track' => 'gitlab'],
])
@php($footerNav = [
    ['projects.index', __('site.nav.work')],
    ['services', __('site.nav.services')],
    ['about', __('site.nav.about')],
    ['contact', __('site.nav.contact')],
])

<footer>
    {{-- ═════════ Closing: back to one spark ═════════ --}}
    @unless (request()->routeIs('contact'))
        <section class="closing">
            <span class="closing__spark" aria-hidden="true"></span>

            <p class="label" data-reveal>{{ __('site.cta.tag') }}</p>

            <h2 class="d-hero">
                <span class="block" data-words>{{ __('site.cta.l1') }}</span>
                <span class="block" data-words style="--d:.12s"><em class="alt">{{ __('site.cta.alt') }}</em> {{ __('site.cta.l2') }}</span>
            </h2>

            <div class="flex flex-wrap items-end justify-between gap-8">
                <p class="lede max-w-md text-muted" data-reveal>{{ __('site.cta.lead') }}</p>
                <div class="flex items-center gap-4" data-reveal>
                    <a href="{{ route('contact') }}" data-track="cta_contact" data-track-from="closing" class="rbtn rbtn--ghost" data-magnetic><span>{{ __('site.cta.form') }}</span></a>
                    <a href="{{ whatsapp_url() }}" target="_blank" rel="noopener" data-track="whatsapp" data-track-from="closing" class="rbtn rbtn--wa" data-magnetic>
                        <span><x-icon.whatsapp class="mx-auto mb-1 h-6 w-6" />{{ __('site.cta.whatsapp') }}</span>
                    </a>
                </div>
            </div>
        </section>
    @endunless

    <div class="foot">
        <div>
            <a href="{{ route('home') }}" class="nav__brand"><x-logo class="nav__mark" /><span>{{ __('common.brand') }}</span></a>
            <p class="mt-4 max-w-xs text-sm text-muted">{{ __('site.footer.tagline') }}</p>
        </div>

        <nav aria-label="{{ __('site.footer.pages') }}">
            <p class="label">{{ __('site.footer.pages') }}</p>
            <ul class="mt-3 space-y-1.5">
                @foreach ($footerNav as [$name, $label])
                    <li><a href="{{ route($name) }}" class="nav__link">{{ $label }}</a></li>
                @endforeach
            </ul>
        </nav>

        <div>
            <p class="label">{{ __('site.footer.reach') }}</p>
            <ul class="mt-3 space-y-1.5">
                @foreach ($channels as $c)
                    <li><a href="{{ $c['href'] }}" target="_blank" rel="noopener" data-track="{{ $c['track'] }}" data-track-from="footer" class="nav__link">{{ $c['label'] }}</a></li>
                @endforeach
                <li><a href="mailto:{{ $footerEmail }}" data-track="email" data-track-from="footer" class="nav__link break-all" dir="ltr">{{ $footerEmail }}</a></li>
            </ul>
        </div>

        <div class="text-sm text-muted">
            <p class="label">{{ __('site.footer.local') }}</p>
            <p class="num mt-3 text-base text-ink" data-clock>--:--</p>
            <p class="mt-6">© {{ now()->year }} {{ __('common.brand') }}</p>
            <a href="#main" class="nav__link mt-1 inline-block">{{ __('site.footer.top') }} ↑</a>
        </div>
    </div>
</footer>
