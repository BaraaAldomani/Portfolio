@php($footerEmail = setting('contact.email', config('portfolio.email')))
@php($channels = [
    ['label' => __('common.whatsapp'), 'href' => 'https://wa.me/' . setting('contact.whatsapp', config('portfolio.whatsapp'))],
    ['label' => __('common.linkedin'), 'href' => setting('contact.linkedin', config('portfolio.linkedin'))],
    ['label' => __('common.gitlab'), 'href' => setting('contact.gitlab', config('portfolio.gitlab'))],
])
@php($footerNav = [
    ['services', __('nav.services')],
    ['projects.index', __('nav.projects')],
    ['about', __('nav.about')],
    ['contact', __('nav.contact')],
])

<footer class="mt-24 border-t border-line bg-surface-muted">
    {{-- CTA band --}}
    <section class="mx-auto max-w-6xl px-5 py-20 sm:px-8 sm:py-28">
        <div class="reveal grid items-end gap-8 lg:grid-cols-[1.5fr_1fr]">
            <div>
                <h2 class="text-4xl font-extrabold tracking-tight text-ink sm:text-5xl">{{ setting_text('contact.cta_title', 'v3.cta.title') }}</h2>
                <p class="mt-4 max-w-lg text-lg leading-relaxed text-muted">{{ setting_text('contact.cta_lead', 'v3.cta.lead') }}</p>
            </div>
            <div class="flex flex-wrap gap-3 lg:justify-end">
                <a href="{{ route('contact') }}" class="js-magnetic btn-press inline-flex items-center justify-center rounded-full bg-primary px-7 py-3.5 text-sm font-bold text-[#08121b] shadow-lg shadow-primary-600/25 transition-colors hover:bg-primary-400">{{ setting_text('contact.cta_primary', 'v3.cta.primary') }}</a>
                <a href="mailto:{{ $footerEmail }}" class="btn-press inline-flex items-center justify-center rounded-full border border-line bg-surface px-7 py-3.5 text-sm font-bold text-ink hover:border-primary-300 hover:text-primary-700">{{ __('common.email_me') }}</a>
            </div>
        </div>
    </section>

    {{-- columns --}}
    <div class="mx-auto grid max-w-6xl gap-10 px-5 py-14 sm:px-8 sm:grid-cols-2 lg:grid-cols-4">
        <div class="sm:col-span-2">
            <div class="flex items-center gap-3">
                <img src="{{ image_url(setting('images.logo'), 'portfolio.images.logo') }}" alt="" width="38" height="35" class="h-9 w-auto">
                <span class="text-base font-extrabold text-ink">{{ __('common.brand') }}</span>
            </div>
            <p class="mt-4 max-w-xs text-sm leading-relaxed text-muted">{{ __('common.footer_tagline') }}</p>
        </div>

        <nav aria-label="{{ __('nav.main_navigation') }}">
            <ul class="space-y-2.5 text-sm">
                @foreach ($footerNav as [$name, $label])
                    <li><a href="{{ route($name) }}" class="font-semibold text-muted hover:text-primary-700">{{ $label }}</a></li>
                @endforeach
            </ul>
        </nav>

        <div class="text-sm">
            <ul class="space-y-2.5">
                @foreach ($channels as $c)
                    <li><a href="{{ $c['href'] }}" target="_blank" rel="noopener" class="font-semibold text-muted hover:text-primary-700">{{ $c['label'] }}</a></li>
                @endforeach
                <li><a href="mailto:{{ $footerEmail }}" class="break-all font-semibold text-primary-700 hover:text-primary-800">{{ $footerEmail }}</a></li>
            </ul>
        </div>
    </div>

    <div class="border-t border-line">
        <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-3 px-5 py-6 sm:flex-row sm:px-8">
            <p class="text-xs text-muted">© {{ now()->year }} {{ __('common.brand') }} · {{ __('common.footer_rights') }}</p>
            <a href="#main" class="text-xs font-bold text-muted hover:text-primary-700">{{ __('common.back_to_top') }} ↑</a>
        </div>
    </div>
</footer>
