<?php

namespace App\Support\Seo;

use App\Models\Project;
use App\Support\ServicePages;
use Illuminate\Support\Collection;

/**
 * Structured data (schema.org JSON-LD).
 *
 * One interlinked graph per page: who (Person), what he sells
 * (ProfessionalService with a priced offer catalogue), the site, this page and
 * where it sits (WebPage + BreadcrumbList), and whatever the page itself adds
 * (a project, the list of projects, one service, its questions and answers).
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
            'genre' => array_values(array_filter(array_map('trim', explode('·', (string) $project->localized('sector'))))) ?: null,
            'dateCreated' => preg_match('/\d{4}/', (string) $project->year, $m) ? $m[0] : null,
            'dateModified' => $project->updated_at?->toDateString(),
            'inLanguage' => app()->getLocale(),
            'creator' => ['@id' => url('/') . '#person'],
            'mainEntityOfPage' => ['@id' => $url . '#webpage'],
            // This page tells the story of the work; the live site is the work
            // itself, so it is given as an example of it, not as "the same thing".
            'workExample' => $project->live_url ? ['@type' => 'WebSite', 'name' => $project->name(), 'url' => $project->live_url] : null,
            'keywords' => implode(', ', $project->highlights()) ?: null,
        ]);
    }

    /**
     * One service on its own page: what it is, who provides it, and from what
     * price. The price is read from the price guide, like everywhere else.
     *
     * @param array<string, mixed> $content the page's words (ServicePages::content)
     */
    public static function service(string $key, string $url, array $content): array
    {
        return [
            '@type' => 'Service',
            '@id' => $url . '#service',
            'name' => $content['name'],
            'serviceType' => __("site.offers.items.$key.title"),
            'description' => $content['answer'],
            'url' => $url,
            'provider' => ['@id' => url('/') . '#service'],
            'areaServed' => ['@type' => 'Country', 'name' => 'Saudi Arabia'],
            'offers' => [
                '@type' => 'Offer',
                'url' => $url,
                'priceCurrency' => 'SAR',
                'priceSpecification' => [
                    '@type' => 'PriceSpecification',
                    'minPrice' => (int) config("pricing.offers.$key.from"),
                    'priceCurrency' => 'SAR',
                ],
            ],
        ];
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
            // The other language's spelling, and the Arabic name as people type
            // it in a search box, without the vowel mark.
            'alternateName' => [app()->getLocale() === 'ar' ? 'Baraa Aldomani' : 'بَراء الدوماني', 'براء الدوماني'],
            'jobTitle' => __('seo.site.job_title'),
            'description' => __('seo.site.service_description'),
            'url' => route('home', ['locale' => app()->getLocale()]),
            'image' => image_url(setting('images.portrait'), 'portfolio.images.portrait'),
            'email' => setting('contact.email', config('portfolio.email')),
            'address' => self::riyadhAddress(),
            'worksFor' => ['@id' => url('/') . '#service'],
            'sameAs' => self::sameAs(),
            'knowsAbout' => ['Software Engineering', 'Web Development', 'Mobile App Development', 'Laravel', 'PHP', 'REST APIs', 'System Design', 'Booking Platforms', 'E-commerce', 'Payment Integrations', 'AI Automation'],
            'knowsLanguage' => ['Arabic', 'English'],
            'alumniOf' => ['@type' => 'CollegeOrUniversity', 'name' => 'Damascus University', 'alternateName' => 'جامعة دمشق'],
        ]);
    }

    private static function professionalService(): array
    {
        $offers = (array) config('pricing.offers');
        $from = $offers ? min(array_column($offers, 'from')) : null;

        return array_filter([
            '@type' => 'ProfessionalService',
            '@id' => url('/') . '#service',
            // The name a client knows him by; the descriptive phrase is an alternate.
            'name' => __('common.brand'),
            'alternateName' => __('seo.site.service_name'),
            'description' => __('seo.site.service_description'),
            'url' => route('home', ['locale' => app()->getLocale()]),
            'logo' => asset('images/logo-512.png'),
            'image' => share_image(),
            'email' => setting('contact.email', config('portfolio.email')),
            'telephone' => '+' . ltrim((string) setting('contact.whatsapp', config('portfolio.whatsapp')), '+'),
            'founder' => ['@id' => url('/') . '#person'],
            'address' => self::riyadhAddress(),
            'areaServed' => [['@type' => 'City', 'name' => 'Riyadh'], ['@type' => 'Country', 'name' => 'Saudi Arabia']],
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
        return [
            '@type' => 'OfferCatalog',
            'name' => __('site.pages.services.tag'),
            'itemListElement' => collect($offers)->map(fn (array $price, string $key): array => [
                '@type' => 'Offer',
                'url' => ServicePages::url($key),
                'priceCurrency' => 'SAR',
                'priceSpecification' => [
                    '@type' => 'PriceSpecification',
                    'minPrice' => $price['from'],
                    'priceCurrency' => 'SAR',
                ],
                // The service is described once, on its own page; here it is
                // named and pointed at, so the two never drift apart.
                'itemOffered' => [
                    '@type' => 'Service',
                    '@id' => ServicePages::url($key) . '#service',
                    'name' => __("service_pages.items.$key.name"),
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
            'mainEntity' => match (true) {
                request()->routeIs('about') => ['@id' => url('/') . '#person'],
                request()->routeIs('services.show') => ['@id' => $meta->canonical . '#service'],
                request()->routeIs('projects.show') => ['@id' => $meta->canonical . '#project'],
                request()->routeIs('projects.index') => ['@id' => $meta->canonical . '#projects'],
                default => null,
            },
            'primaryImageOfPage' => $meta->image,
            // The price pages carry the day the price guide was last checked.
            'dateModified' => request()->routeIs('services', 'services.show') ? config('pricing.reviewed') : null,
            'breadcrumb' => $hasTrail ? ['@id' => $meta->canonical . '#breadcrumb'] : null,
        ]);
    }

    /** Home > section (> project or service). Nothing on the home page itself. */
    private static function breadcrumbs(PageMeta $meta): ?array
    {
        if (request()->routeIs('home')) {
            return null;
        }

        $locale = app()->getLocale();
        $trail = [[__('nav.home'), route('home', ['locale' => $locale])]];

        $section = match (true) {
            request()->routeIs('services', 'services.show') => [__('site.nav.services'), route('services', ['locale' => $locale])],
            request()->routeIs('projects.*') => [__('site.nav.work'), route('projects.index', ['locale' => $locale])],
            request()->routeIs('about') => [__('site.nav.about'), route('about', ['locale' => $locale])],
            request()->routeIs('contact') => [__('site.nav.contact'), route('contact', ['locale' => $locale])],
            default => null,
        };
        if ($section) {
            $trail[] = $section;
        }
        if (request()->routeIs('projects.show', 'services.show')) {
            $trail[] = [$meta->name ?? $meta->title, $meta->canonical];
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
    public static function faqPage(array $items, string $url): array
    {
        return [
            '@type' => 'FAQPage',
            '@id' => $url . '#faq',
            'isPartOf' => ['@id' => $url . '#webpage'],
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
