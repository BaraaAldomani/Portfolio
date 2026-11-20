<x-layout :meta="$meta" page="contact">
    @php($email = setting('contact.email', config('portfolio.email')))

    <x-page-head class="!min-h-[56svh]" :tag="__('site.pages.contact.tag')" :title="setting_text('contact.page_title', 'contact.title')" :lead="setting_text('contact.page_lead', 'contact.lead')" />

    <section class="wrap grid gap-x-10 gap-y-14 pb-28 lg:grid-cols-12">
        {{-- ═════════ Direct channels ═════════ --}}
        <div class="lg:col-span-6">
            <div class="rows">
                <a href="{{ whatsapp_url() }}" target="_blank" rel="noopener" data-track="whatsapp" data-track-from="contact" class="row !grid-cols-[1fr_auto] !items-center" data-reveal>
                    <span>
                        <span class="label block">{{ setting_text('contact.whatsapp_title', 'contact.whatsapp_title') }} · {{ __('site.pages.contact.fastest') }}</span>
                        <span class="row__title mt-1 block !text-[clamp(1.5rem,3vw,3rem)]"><bdi dir="ltr">{{ setting('contact.phone_display', config('portfolio.phone_display')) }}</bdi></span>
                    </span>
                    <x-icon.whatsapp class="h-9 w-9" />
                </a>
                <a href="mailto:{{ $email }}" data-track="email" data-track-from="contact" class="row !grid-cols-[1fr_auto] !items-center" data-reveal>
                    <span>
                        <span class="label block">{{ setting_text('contact.email_title', 'contact.email_title') }}</span>
                        <span class="row__title mt-1 block break-all !text-[clamp(1.15rem,2vw,2rem)]" dir="ltr">{{ $email }}</span>
                    </span>
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17 17 7M9 7h8v8"/></svg>
                </a>
            </div>
            <p class="mt-6 text-sm text-muted" data-reveal>{{ setting_text('contact.response_note', 'contact.response_note') }}</p>
        </div>

        {{-- ═════════ Form ═════════ --}}
        <div class="lg:col-span-5 lg:col-start-8">
            @if (session('contact_sent'))
                <div class="mb-8 rounded-2xl border border-success/40 px-5 py-4 font-semibold text-success" role="status" data-track-view="contact_form_submit">
                    {{ __('contact.form.success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('contact.store') }}" data-reveal>
                @csrf
                <h2 class="d-3">{{ __('site.pages.contact.or') }}</h2>

                {{-- Honeypot: real visitors never see or fill this field. --}}
                <div class="sr-only" aria-hidden="true">
                    <label>{{ __('common.brand') }}<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                </div>

                <div class="mt-6 space-y-6">
                    <div>
                        <label for="name" class="label">{{ __('contact.form.name') }}</label>
                        <input id="name" name="name" type="text" required autocomplete="name" value="{{ old('name') }}"
                               placeholder="{{ __('contact.form.name_placeholder') }}" class="field @error('name') has-error @enderror">
                        @error('name') <p class="mt-1 text-sm font-semibold text-danger">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="label">{{ __('contact.form.email') }}</label>
                        <input id="email" name="email" type="email" required autocomplete="email" value="{{ old('email') }}" dir="ltr"
                               placeholder="{{ __('contact.form.email_placeholder') }}" class="field text-end @error('email') has-error @enderror">
                        @error('email') <p class="mt-1 text-sm font-semibold text-danger">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="message" class="label">{{ __('contact.form.message') }}</label>
                        <textarea id="message" name="message" rows="5" required
                                  placeholder="{{ __('contact.form.message_placeholder') }}" class="field @error('message') has-error @enderror">{{ old('message') }}</textarea>
                        @error('message') <p class="mt-1 text-sm font-semibold text-danger">{{ $message }}</p> @enderror
                    </div>

                    @error('website') <p class="text-sm font-semibold text-danger">{{ $message }}</p> @enderror

                    <button type="submit" class="cta">{{ __('contact.form.submit') }}</button>
                </div>
            </form>
        </div>
    </section>
</x-layout>
