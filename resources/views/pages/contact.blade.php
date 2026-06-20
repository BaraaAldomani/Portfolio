<x-layout :meta="$meta">
    <x-section>
        <header class="reveal max-w-2xl">
            <p class="text-sm font-bold tracking-wide text-primary-700">{{ __('contact.eyebrow') }}</p>
            <h1 data-reveal-text class="mt-2 text-3xl font-extrabold text-ink sm:text-4xl">{{ __('contact.title') }}</h1>
            <p class="mt-4 text-lg leading-relaxed text-muted">{{ __('contact.lead') }}</p>
        </header>

        <div class="mt-12 grid gap-10 lg:grid-cols-5">
            {{-- Direct channels --}}
            <div class="space-y-4 lg:col-span-2" data-stagger>
                <a href="https://wa.me/{{ config('portfolio.whatsapp') }}" target="_blank" rel="noopener"
                   class="reveal-start glow-border block rounded-2xl border border-line bg-surface p-6 transition-all duration-300 hover:-translate-y-1 hover:border-primary-200 hover:shadow-lg hover:shadow-primary-600/10">
                    <p class="font-extrabold text-ink">{{ __('contact.whatsapp_title') }}</p>
                    <p class="mt-1 text-sm text-muted">{{ __('contact.whatsapp_text') }}</p>
                    <p class="mt-3 text-sm font-bold text-primary-700">{{ config('portfolio.phone_display') }}</p>
                </a>

                <a href="mailto:{{ config('portfolio.email') }}"
                   class="reveal-start glow-border block rounded-2xl border border-line bg-surface p-6 transition-all duration-300 hover:-translate-y-1 hover:border-primary-200 hover:shadow-lg hover:shadow-primary-600/10">
                    <p class="font-extrabold text-ink">{{ __('contact.email_title') }}</p>
                    <p class="mt-1 text-sm text-muted">{{ __('contact.email_text') }}</p>
                    <p class="mt-3 break-all text-sm font-bold text-primary-700">{{ config('portfolio.email') }}</p>
                </a>

                <p class="reveal-start px-1 text-sm text-muted">{{ __('contact.response_note') }}</p>
            </div>

            {{-- Form --}}
            <div class="lg:col-span-3">
                @if (session('contact_sent'))
                    <div class="reveal mb-6 rounded-xl border border-success/30 bg-success-soft px-4 py-3 text-sm font-semibold text-success" role="status">
                        {{ __('contact.form.success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('contact.store') }}" class="reveal rounded-2xl border border-line bg-surface p-6 sm:p-8">
                    @csrf
                    <h2 class="text-lg font-extrabold text-ink">{{ __('contact.form.title') }}</h2>

                    {{-- Honeypot: visually hidden. Uses sr-only (NOT left:-9999px, which
                         inflated the page width and broke the RTL layout). Bots still fill it. --}}
                    <div class="sr-only" aria-hidden="true">
                        <label>{{ __('common.brand') }}<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                    </div>

                    <div class="mt-5 space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-bold text-ink">{{ __('contact.form.name') }}</label>
                            <input id="name" name="name" type="text" required value="{{ old('name') }}"
                                   placeholder="{{ __('contact.form.name_placeholder') }}"
                                   class="mt-1.5 w-full rounded-xl border border-line bg-surface px-4 py-2.5 text-ink outline-none transition-colors focus:border-primary-400 @error('name') border-danger @enderror">
                            @error('name') <p class="mt-1 text-xs font-semibold text-danger">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-bold text-ink">{{ __('contact.form.email') }}</label>
                            <input id="email" name="email" type="email" required value="{{ old('email') }}"
                                   placeholder="{{ __('contact.form.email_placeholder') }}" dir="ltr"
                                   class="mt-1.5 w-full rounded-xl border border-line bg-surface px-4 py-2.5 text-ink outline-none transition-colors focus:border-primary-400 @error('email') border-danger @enderror">
                            @error('email') <p class="mt-1 text-xs font-semibold text-danger">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-bold text-ink">{{ __('contact.form.message') }}</label>
                            <textarea id="message" name="message" rows="5" required
                                      placeholder="{{ __('contact.form.message_placeholder') }}"
                                      class="mt-1.5 w-full rounded-xl border border-line bg-surface px-4 py-2.5 text-ink outline-none transition-colors focus:border-primary-400 @error('message') border-danger @enderror">{{ old('message') }}</textarea>
                            @error('message') <p class="mt-1 text-xs font-semibold text-danger">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit"
                                class="js-magnetic btn-press inline-flex w-full items-center justify-center gap-2 rounded-full bg-primary px-7 py-3.5 text-sm font-bold text-white shadow-lg shadow-primary-600/25 transition-colors hover:bg-primary-700 sm:w-auto">
                            {{ __('contact.form.submit') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </x-section>
</x-layout>
