<?php

namespace App\Support\Seo;

use App\Models\Project;
use Illuminate\Support\Collection;

/**
 * Structured data (schema.org JSON-LD).
 *
 * One interlinked graph per page: who (Person), what he sells
 * (ProfessionalService with a priced offer catalogue), the site, this page and
 * where it sits (WebPage + BreadcrumbList), and whatever the page itself adds
 * (a project, the list of projects, its questions and answers).
 *
 * Search engines use it for rich results and the knowledge panel; answer
 * engines and AI assistants read it to identify the person, quote prices and
 * cite the right page. Everything here is also visible on the page.
 */
final class JsonLd
{
    public static function forPage(PageMeta $meta): array
    {
        $crumbs = self::breadcrumbs($meta);

        $nodes = array_filter([
            self::person($meta->description),
            self::professionalService(),
            self::webSite(),
            self::webPage($meta, $crumbs !== null),
            $crumbs,
        ]);

        // Questions are marked up only where the visitor can read them.
        if (request()->routeIs('home', 'services')) {
            $nodes[] = self::faqPage(faq_items(), $meta->canonical);
        } elseif (request()->routeIs('about')) {
            $nodes[] = self::faqPage((array) __('seo.faq'), $meta->canonical);
        }

        return [
            '@context' => 'https://schema.org',
            '@graph' => [...$nodes, ...$meta->schema],
        ];
    }

    /** A case study: what was built, for which sector, and where it lives. */
    public static function project(Project $project, string $url): array
    {
        return array_filter([
            '@type' => 'CreativeWork',
            '@id' => $url . '#project',
            'name' => $project->name(),
            'headline' => $project->localized('title'),
            'description' => $project->localized('summary'),
            'url' => $url,
            'image' => $project->hasCover() ? $project->coverImage() : null,
            'genre' => $project->localized('sector'),
            'dateCreated' => preg_match('/\d{4}/', (string) $project->year, $m) ? $m[0] : null,
            'inLanguage' => app()->getLocale(),
            'creator' => ['@id' => url('/') . '#person'],
            'sameAs' => $project->live_url ?: null,
            'keywords' => implode(', ', $project->highlights()) ?: null,
        ]);
    }

    /** @param Collection<int, Project> $projects */
    public static function projectList(Collection $projects): array
    {
        return [
            '@type' => 'ItemList',
            '@id' => route('projects.index', ['locale' => app()->getLocale()]) . '#projects',
            'name' => __('site.pages.work.title'),
            'itemListElement' => $projects->values()->map(fn (Project $project, int $i): array => [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $project->name(),
                'url' => route('projects.show', ['locale' => app()->getLocale(), 'project' => $project->slug()]),
            ])->all(),
        ];
    }

    private static function person(?string $description): array
    {
        return array_filter([
            '@type' => 'Person',
            '@id' => url('/') . '#person',
            'name' => __('common.brand'),
            'alternateName' => app()->getLocale() === 'ar' ? 'Baraa Aldomani' : 'بَراء الدوماني',
            'jobTitle' => __('seo.site.job_title'),
            'description' => __('seo.site.service_description'),
            'url' => route('home', ['locale' => app()->getLocale()]),
            'image' => image_url(setting('images.portrait'), 'portfolio.images.portrait'),
            'email' => 'mailto:' . setting('contact.email', config('portfolio.email')),
            'address' => self::riyadhAddress(),
            'sameAs' => self::sameAs(),
            'knowsAbout' => ['Software Engineering', 'Web Development', 'Mobile App Development', 'Laravel', 'PHP', 'REST APIs', 'System Design', 'Booking Platforms', 'E-commerce', 'Payment Integrations', 'AI Automation'],
            'knowsLanguage' => ['Arabic', 'English'],
            'alumniOf' => 'Damascus University',
        ]);
    }

    private static function professionalService(): array
    {
        $offers = (array) config('pricing.offers');
        $from = $offers ? min(array_column($offers, 'from')) : null;

        return array_filter([
            '@type' => 'ProfessionalService',
            '@id' => url('/') . '#service',
            'name' => __('seo.site.service_name'),
            'description' => __('seo.site.service_description'),
            'url' => route('home', ['locale' => app()->getLocale()]),
            'image' => asset('images/og.png'),
            'email' => setting('contact.email', config('portfolio.email')),
            'telephone' => '+' . ltrim((string) setting('contact.whatsapp', config('portfolio.whatsapp')), '+'),
            'founder' => ['@id' => url('/') . '#person'],
            'address' => self::riyadhAddress(),
            'areaServed' => ['@type' => 'Country', 'name' => 'Saudi Arabia'],
            'knowsLanguage' => ['Arabic', 'English'],
            'priceRange' => $from ? 'SAR ' . number_format($from) . '+' : null,
            'hasOfferCatalog' => $offers ? self::offerCatalog($offers) : null,
        ]);
    }

    /**
     * The public price guide (config/pricing.php): one offer per service, with
     * its starting price. This is what lets an answer engine say "from 3,500
     * SAR" and name the source.
     *
     * @param array<string, array{from: int, to?: int}> $offers
     */
    private static function offerCatalog(array $offers): array
    {
        $services = route('services', ['locale' => app()->getLocale()]);

        return [
            '@type' => 'OfferCatalog',
            'name' => __('site.pages.services.tag'),
            'itemListElement' => collect($offers)->map(fn (array $price, string $key): array => [
                '@type' => 'Offer',
                'url' => $services . '#' . $key,
                'priceCurrency' => 'SAR',
                'priceSpecification' => [
                    '@type' => 'PriceSpecification',
                    'minPrice' => $price['from'],
                    'priceCurrency' => 'SAR',
                ],
                'itemOffered' => [
                    '@type' => 'Service',
                    'name' => __("site.offers.items.$key.title"),
                    'description' => __("site.offers.items.$key.blurb"),
                    'provider' => ['@id' => url('/') . '#person'],
                    'areaServed' => ['@type' => 'Country', 'name' => 'Saudi Arabia'],
                ],
            ])->values()->all(),
        ];
    }

    private static function webSite(): array
    {
        return [
            '@type' => 'WebSite',
            '@id' => url('/') . '#website',
            // Concise brand: this is what Google shows as the site name.
            // The longer descriptive form stays in alternateName.
            'name' => __('common.brand'),
            'alternateName' => __('seo.site.name'),
            'url' => url('/'),
            'inLanguage' => ['ar', 'en'],
            'publisher' => ['@id' => url('/') . '#person'],
        ];
    }

    private static function webPage(PageMeta $meta, bool $hasTrail): array
    {
        $type = match (true) {
            request()->routeIs('about') => 'ProfilePage',
            request()->routeIs('contact') => 'ContactPage',
            request()->routeIs('projects.index') => 'CollectionPage',
            default => 'WebPage',
        };

        return array_filter([
            '@type' => $type,
            '@id' => $meta->canonical . '#webpage',
            'url' => $meta->canonical,
            'name' => $meta->title,
            'description' => $meta->description,
            'inLanguage' => app()->getLocale(),
            'isPartOf' => ['@id' => url('/') . '#website'],
            'about' => ['@id' => url('/') . '#person'],
            'mainEntity' => request()->routeIs('about') ? ['@id' => url('/') . '#person'] : null,
            'primaryImageOfPage' => $meta->image,
            'breadcrumb' => $hasTrail ? ['@id' => $meta->canonical . '#breadcrumb'] : null,
        ]);
    }

    /** Home > section (> project). Nothing on the home page itself. */
    private static function breadcrumbs(PageMeta $meta): ?array
    {
        if (request()->routeIs('home')) {
            return null;
        }

        $locale = app()->getLocale();
        $trail = [[__('nav.home'), route('home', ['locale' => $locale])]];

        $section = match (true) {
            request()->routeIs('services') => [__('site.nav.services'), route('services', ['locale' => $locale])],
            request()->routeIs('projects.*') => [__('site.nav.work'), route('projects.index', ['locale' => $locale])],
            request()->routeIs('about') => [__('site.nav.about'), route('about', ['locale' => $locale])],
            request()->routeIs('contact') => [__('site.nav.contact'), route('contact', ['locale' => $locale])],
            default => null,
        };
        if ($section) {
            $trail[] = $section;
        }
        if (request()->routeIs('projects.show')) {
            $trail[] = [$meta->title, $meta->canonical];
        }

        // A trail of one (a page outside the main sections) says nothing.
        if (count($trail) < 2) {
            return null;
        }

        return [
            '@type' => 'BreadcrumbList',
            '@id' => $meta->canonical . '#breadcrumb',
            'itemListElement' => array_map(fn (array $crumb, int $i): array => [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $crumb[0],
                'item' => $crumb[1],
            ], $trail, array_keys($trail)),
        ];
    }

    /**
     * Questions and answers exactly as the visitor reads them: eligible for
     * rich results, and the format answer engines lift verbatim.
     *
     * @param array<int, array{q: string, a: string}> $items
     */
    private static function faqPage(array $items, string $url): array
    {
        return [
            '@type' => 'FAQPage',
            '@id' => $url . '#faq',
            'inLanguage' => app()->getLocale(),
            'mainEntity' => array_values(array_map(fn (array $qa): array => [
                '@type' => 'Question',
                'name' => $qa['q'],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $qa['a']],
            ], $items)),
        ];
    }

    /**
     * Verified profiles that point back to the same identity. Only links that
     * are actually set are listed. Editable from the dashboard (Contact & social).
     *
     * @return array<int, string>
     */
    private static function sameAs(): array
    {
        return array_values(array_filter([
            setting('contact.linkedin', config('portfolio.linkedin')),
            setting('contact.gitlab', config('portfolio.gitlab')),
            setting('contact.github', config('portfolio.github')),
            setting('contact.twitter', config('portfolio.twitter')),
            setting('contact.stackoverflow', config('portfolio.stackoverflow')),
            setting('contact.youtube', config('portfolio.youtube')),
        ]));
    }

    private static function riyadhAddress(): array
    {
        return [
            '@type' => 'PostalAddress',
            'addressLocality' => 'Riyadh',
            'addressCountry' => 'SA',
        ];
    }
}
