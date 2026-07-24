<?php

namespace App\Support\Seo;

final class JsonLd
{
    /**
     * Structured data present on every page: Person + ProfessionalService +
     * WebSite + FAQ. A rich, interlinked entity graph is what search engines
     * use to build a Knowledge Panel and what AI assistants read to identify
     * and cite the person.
     *
     * @param string|null $description page description, used for the Person node
     */
    public static function siteGraph(?string $description = null): array
    {
        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                self::person($description),
                self::professionalService(),
                self::webSite(),
                self::faqPage(),
            ],
        ];
    }

    private static function person(?string $description): array
    {
        return array_filter([
            '@type' => 'Person',
            '@id' => url('/') . '#person',
            'name' => __('common.brand'),
            'jobTitle' => __('seo.site.job_title'),
            'description' => $description ?: __('seo.site.service_description'),
            'url' => route('home', ['locale' => app()->getLocale()]),
            'image' => image_url(setting('images.portrait'), 'portfolio.images.portrait'),
            'email' => 'mailto:' . setting('contact.email', config('portfolio.email')),
            'address' => self::riyadhAddress(),
            'sameAs' => self::sameAs(),
            'knowsAbout' => ['Software Engineering', 'Backend Development', 'Laravel', 'PHP', 'REST APIs', 'System Design', 'Scalable Web Applications', 'Databases', 'Payment Integrations'],
            'knowsLanguage' => ['Arabic', 'English'],
            'alumniOf' => 'Damascus University',
        ]);
    }

    private static function professionalService(): array
    {
        return [
            '@type' => 'ProfessionalService',
            '@id' => url('/') . '#service',
            'name' => __('seo.site.service_name'),
            'description' => __('seo.site.service_description'),
            'url' => route('home', ['locale' => app()->getLocale()]),
            'email' => setting('contact.email', config('portfolio.email')),
            'founder' => ['@id' => url('/') . '#person'],
            'provider' => ['@id' => url('/') . '#person'],
            'address' => self::riyadhAddress(),
            'areaServed' => ['@type' => 'Country', 'name' => 'Saudi Arabia'],
            'knowsLanguage' => ['Arabic', 'English'],
            'priceRange' => '$$',
        ];
    }

    private static function webSite(): array
    {
        return [
            '@type' => 'WebSite',
            '@id' => url('/') . '#website',
            'name' => __('seo.site.name'),
            'url' => url('/'),
            'inLanguage' => ['ar', 'en'],
            'publisher' => ['@id' => url('/') . '#person'],
        ];
    }

    /**
     * Answer-shaped Q&A — eligible for FAQ rich results and exactly the format
     * AI assistants lift verbatim. Questions/answers live in lang/{locale}/seo.php.
     */
    private static function faqPage(): array
    {
        $items = (array) __('seo.faq');

        $questions = array_map(fn (array $qa): array => [
            '@type' => 'Question',
            'name' => $qa['q'],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $qa['a']],
        ], $items);

        return [
            '@type' => 'FAQPage',
            '@id' => url('/') . '#faq',
            'inLanguage' => app()->getLocale(),
            'mainEntity' => array_values($questions),
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
