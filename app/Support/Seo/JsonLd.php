<?php

namespace App\Support\Seo;

final class JsonLd
{
    /**
     * Schemas present on every page: Person + ProfessionalService + WebSite.
     */
    public static function siteGraph(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@graph' => [self::person(), self::professionalService(), self::webSite()],
        ];
    }

    private static function person(): array
    {
        return [
            '@type' => 'Person',
            '@id' => url('/') . '#person',
            'name' => __('seo.site.author'),
            'jobTitle' => __('seo.site.job_title'),
            'email' => 'mailto:' . config('portfolio.email'),
            'url' => route('home', ['locale' => app()->getLocale()]),
            'address' => self::riyadhAddress(),
            'sameAs' => [config('portfolio.linkedin'), config('portfolio.gitlab')],
            'alumniOf' => 'Damascus University',
        ];
    }

    private static function professionalService(): array
    {
        return [
            '@type' => 'ProfessionalService',
            '@id' => url('/') . '#service',
            'name' => __('seo.site.service_name'),
            'description' => __('seo.site.service_description'),
            'url' => route('home', ['locale' => app()->getLocale()]),
            'email' => config('portfolio.email'),
            'founder' => ['@id' => url('/') . '#person'],
            'address' => self::riyadhAddress(),
            'areaServed' => ['@type' => 'Country', 'name' => 'Saudi Arabia'],
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

    private static function riyadhAddress(): array
    {
        return [
            '@type' => 'PostalAddress',
            'addressLocality' => 'Riyadh',
            'addressCountry' => 'SA',
        ];
    }
}
