<?php

use App\Support\SiteContent;
use Illuminate\Support\Facades\Storage;

if (! function_exists('setting')) {
    /**
     * Read a dashboard-managed setting by "group.key", with a fallback default.
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return app(SiteContent::class)->setting($key, $default);
    }
}

if (! function_exists('setting_text')) {
    /**
     * Read a bilingual setting for the current locale, e.g.
     * setting_text('home.hero_title', 'v3.hero.title') reads
     * "home.hero_title_{locale}" and falls back to the given lang key.
     */
    function setting_text(string $baseKey, ?string $fallbackLangKey = null): string
    {
        $default = $fallbackLangKey !== null ? trans($fallbackLangKey) : '';
        $value = setting($baseKey . '_' . app()->getLocale(), $default);

        return is_string($value) ? $value : (string) $value;
    }
}

if (! function_exists('setting_list')) {
    /**
     * Read a bilingual list setting for the current locale (e.g. rakeez points),
     * falling back to the given lang key which must resolve to an array.
     *
     * @return array<int, string>
     */
    function setting_list(string $baseKey, ?string $fallbackLangKey = null): array
    {
        $default = $fallbackLangKey !== null ? (array) trans($fallbackLangKey) : [];
        $value = setting($baseKey . '_' . app()->getLocale(), $default);

        return is_array($value) ? $value : $default;
    }
}

if (! function_exists('image_url')) {
    /**
     * Resolve a public URL for an image path that may be either a
     * dashboard-uploaded file (stored on the `public` disk under
     * storage/app/public) or a legacy asset shipped under public/.
     *
     * When $path is empty, falls back to the given config('portfolio.images.*')
     * default so nothing breaks before an image is uploaded.
     */
    function image_url(?string $path, ?string $fallbackConfigKey = null): string
    {
        $path = $path ?: ($fallbackConfigKey ? config($fallbackConfigKey) : null);

        if (! $path) {
            return '';
        }

        // Absolute URLs pass through untouched.
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        // Uploaded files live on the public disk; legacy assets live under public/.
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        return asset($path);
    }
}

if (! function_exists('whatsapp_url')) {
    /**
     * wa.me deep link to the owner's WhatsApp, pre-filled with a short
     * localized greeting so the visitor only has to press send.
     */
    function whatsapp_url(?string $text = null): string
    {
        $number = preg_replace('/\D+/', '', (string) setting('contact.whatsapp', config('portfolio.whatsapp')));
        $text ??= __('common.whatsapp_prefill');

        return 'https://wa.me/' . $number . ($text !== '' ? '?text=' . rawurlencode($text) : '');
    }
}

if (! function_exists('accent')) {
    /**
     * Headlines may mark ONE word with *asterisks*; it is set in the
     * handwritten accent face. Everything else is escaped.
     */
    function accent(string $text): \Illuminate\Support\HtmlString
    {
        return new \Illuminate\Support\HtmlString(
            preg_replace('/\*(.+?)\*/u', '<em class="alt">$1</em>', e($text))
        );
    }
}
