<?php

namespace App\Support\Seo;

use Illuminate\Support\Facades\Route;

final readonly class PageMeta
{
    /**
     * @param array<string, string> $alternates locale => absolute URL, used for hreflang + language switcher
     */
    public function __construct(
        public string $title,
        public string $description,
        public array $alternates,
        public string $canonical,
    ) {
    }

    /**
     * Build meta for a static page from lang/{locale}/seo.php keys.
     */
    public static function fromLang(string $page): self
    {
        $alternates = self::alternatesFor(Route::currentRouteName(), []);

        return new self(
            title: __("seo.{$page}.title"),
            description: __("seo.{$page}.description"),
            alternates: $alternates,
            canonical: $alternates[app()->getLocale()],
        );
    }

    /**
     * Build meta for a dynamic page; $paramsByLocale carries the localized
     * route params per locale (e.g. each language's slug).
     *
     * @param array<string, array<string, string>> $paramsByLocale
     */
    public static function forContent(string $title, string $description, array $paramsByLocale): self
    {
        $alternates = self::alternatesFor(Route::currentRouteName(), $paramsByLocale);

        return new self($title, $description, $alternates, $alternates[app()->getLocale()]);
    }

    /**
     * @param array<string, array<string, string>> $paramsByLocale
     * @return array<string, string>
     */
    private static function alternatesFor(string $routeName, array $paramsByLocale): array
    {
        return collect(config('app.supported_locales'))
            ->mapWithKeys(fn (string $locale) => [
                $locale => route($routeName, ['locale' => $locale, ...($paramsByLocale[$locale] ?? [])]),
            ])
            ->all();
    }
}
