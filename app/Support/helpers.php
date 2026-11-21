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

if (! function_exists('faq_items')) {
    /**
     * The visible FAQ (lang site.faq.items) with the price guide filled in:
     * ":site_from" and friends become the current numbers from
     * config/pricing.php, so an answer never drifts from the price list.
     *
     * @return array<int, array{q: string, a: string}>
     */
    function faq_items(): array
    {
        $tokens = [];
        foreach ((array) config('pricing.offers') as $key => $offer) {
            $tokens[":{$key}_from"] = number_format($offer['from']);
        }

        return array_map(
            fn (array $qa): array => ['q' => $qa['q'], 'a' => strtr($qa['a'], $tokens)],
            (array) __('site.faq.items'),
        );
    }
}

if (! function_exists('service_url')) {
    /**
     * The page of one service (an offer key from config/pricing.php), in the
     * current locale unless another is given.
     */
    function service_url(string $key, ?string $locale = null): string
    {
        return \App\Support\ServicePages::url($key, $locale);
    }
}

if (! function_exists('weeks_span')) {
    /**
     * A span of weeks the way each language says it. Arabic counts differently
     * for one, two, three to ten and eleven up, so "1 to 2" cannot simply be
     * followed by one word for "weeks".
     */
    function weeks_span(int|float $min, int|float $max, ?string $locale = null): string
    {
        [$min, $max] = [(int) round($min), (int) round($max)];

        if (($locale ?? app()->getLocale()) !== 'ar') {
            return $min === $max ? $min . ' ' . ($min === 1 ? 'week' : 'weeks') : "{$min} to {$max} weeks";
        }

        $unit = fn (int $n): string => match (true) {
            $n === 1 => 'أسبوع',
            $n === 2 => 'أسبوعين',
            $n <= 10 => "{$n} أسابيع",
            default => "{$n} أسبوعاً",
        };

        if ($min === $max) {
            return $unit($min);
        }

        return 'من ' . ($min <= 2 ? $unit($min) : (string) $min) . ' إلى ' . $unit($max);
    }
}

if (! function_exists('share_image')) {
    /**
     * The picture shown when a page without its own is shared. It has the
     * site's address drawn into it, so it follows the address the site is
     * served on, and its headline follows the language.
     */
    function share_image(?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $host = parse_url((string) (config('portfolio.primary_url') ?: url('/')), PHP_URL_HOST);
        // Not dot notation: a host name has dots of its own.
        $own = ((array) config('portfolio.share_images'))[$host][$locale] ?? null;

        return asset($own ?: ($locale === 'en' ? 'images/og-en.png' : 'images/og.png'));
    }
}
