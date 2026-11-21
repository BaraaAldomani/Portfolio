<?php

namespace App\Support\Seo;

use Illuminate\Support\Facades\Route;

final readonly class PageMeta
{
    /**
     * @param array<string, string> $alternates locale => absolute URL, used for hreflang + language switcher
     * @param string|null $image absolute URL of the picture to show when the page is shared
     * @param array<int, array<string, mixed>> $schema extra schema.org nodes this page adds to the site graph
     */
    public function __construct(
        public string $title,
        public string $description,
        public array $alternates,
        public string $canonical,
        public ?string $image = null,
        public array $schema = [],
    ) {
    }

    /**
     * Build meta for a static page from lang/{locale}/seo.php keys.
     *
     * @param array<int, array<string, mixed>> $schema
     */
    public static function fromLang(string $page, array $schema = [], ?string $image = null): self
    {
        $alternates = self::alternatesFor(Route::currentRouteName(), []);

        return new self(
            title: setting_text("seo.{$page}_title", "seo.{$page}.title"),
            description: setting_text("seo.{$page}_description", "seo.{$page}.description"),
            alternates: $alternates,
            canonical: $alternates[app()->getLocale()],
            image: $image,
            schema: $schema,
        );
    }

    /**
     * Build meta for a dynamic page; $paramsByLocale carries the localized
     * route params per locale (e.g. each language's slug).
     *
     * @param array<string, array<string, string>> $paramsByLocale
     * @param array<int, array<string, mixed>> $schema
     */
    public static function forContent(string $title, string $description, array $paramsByLocale, array $schema = [], ?string $image = null): self
    {
        $alternates = self::alternatesFor(Route::currentRouteName(), $paramsByLocale);

        return new self($title, $description, $alternates, $alternates[app()->getLocale()], $image, $schema);
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
