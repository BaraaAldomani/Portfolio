<?php

namespace App\Support;

use App\Models\Project;
use Illuminate\Support\Collection;

/**
 * The six service pages: one per offer in config/pricing.php.
 *
 * Words come from lang/{locale}/service_pages.php, prices and durations from
 * the price guide, and the proof from config/service_pages.php. Nothing about
 * a service is typed in two places.
 */
final class ServicePages
{
    /** @return array<int, string> offer keys, in the order of the price guide */
    public static function keys(): array
    {
        return array_keys((array) config('pricing.offers'));
    }

    public static function slug(string $key, ?string $locale = null): string
    {
        return (string) trans("service_pages.items.{$key}.slug", [], $locale ?? app()->getLocale());
    }

    public static function keyForSlug(string $slug, ?string $locale = null): ?string
    {
        foreach (self::keys() as $key) {
            if (self::slug($key, $locale) === $slug) {
                return $key;
            }
        }

        return null;
    }

    public static function url(string $key, ?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return route('services.show', ['locale' => $locale, 'service' => self::slug($key, $locale)]);
    }

    /** @return array<string, array<string, string>> locale => route params */
    public static function paramsByLocale(string $key): array
    {
        return collect(config('app.supported_locales'))
            ->mapWithKeys(fn (string $locale): array => [$locale => ['service' => self::slug($key, $locale)]])
            ->all();
    }

    /**
     * The page's words in the current locale, with ":from" and ":time" filled
     * from the price guide so the copy never drifts from the price list.
     *
     * @return array<string, mixed>
     */
    public static function content(string $key, ?string $locale = null): array
    {
        $locale ??= app()->getLocale();
        $tokens = [
            ':from' => number_format((int) config("pricing.offers.{$key}.from")),
            ':time' => (string) trans("site.offers.items.{$key}.time", [], $locale),
        ];

        $content = (array) trans("service_pages.items.{$key}", [], $locale);
        array_walk_recursive($content, function (&$value) use ($tokens): void {
            if (is_string($value)) {
                $value = strtr($value, $tokens);
            }
        });

        return $content;
    }

    /**
     * Real work that is an example of this service, in the configured order.
     *
     * @return Collection<int, Project>
     */
    public static function projects(string $key): Collection
    {
        $slugs = (array) config("service_pages.proof.{$key}", []);
        if ($slugs === []) {
            return collect();
        }

        $found = Project::whereIn('slug_en', $slugs)->get()->keyBy('slug_en');

        return collect($slugs)->map(fn (string $slug) => $found->get($slug))->filter()->values();
    }

    /** The service a project is first listed under, if any. */
    public static function keyForProject(Project $project): ?string
    {
        foreach (self::keys() as $key) {
            if (in_array($project->slug_en, (array) config("service_pages.proof.{$key}", []), true)) {
                return $key;
            }
        }

        return null;
    }
}
