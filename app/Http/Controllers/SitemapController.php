<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Support\ServicePages;
use Illuminate\Http\Response;

final class SitemapController
{
    /** Static routes that exist in every locale. */
    // The blog is left out until it has articles: an empty page is not worth a crawl.
    private const STATIC_ROUTES = ['home', 'services', 'projects.index', 'about', 'contact'];

    public function index(): Response
    {
        return response()
            ->view('seo.sitemap', ['urls' => $this->urls()])
            ->header('Content-Type', 'application/xml');
    }

    public function robots(): Response
    {
        return response()
            ->view('seo.robots', ['sitemap' => route('sitemap')])
            ->header('Content-Type', 'text/plain');
    }

    /** The index: who he is and where each thing is, as Markdown links (llmstxt.org). */
    public function llms(): Response
    {
        return response()
            ->view('seo.llms', $this->llmsData())
            ->header('Content-Type', 'text/plain; charset=utf-8');
    }

    /** Everything an assistant may quote, in one file: services in detail, questions, work. */
    public function llmsFull(): Response
    {
        return response()
            ->view('seo.llms-full', $this->llmsData())
            ->header('Content-Type', 'text/plain; charset=utf-8');
    }

    /**
     * What both files are written from: the same price guide, copy and
     * projects the pages use, in English with the Arabic names beside them.
     *
     * @return array<string, mixed>
     */
    private function llmsData(): array
    {
        $offers = (array) config('pricing.offers');

        $tokens = [];
        foreach ($offers as $key => $offer) {
            $tokens[":{$key}_from"] = number_format($offer['from']);
        }

        return [
            'offers' => $offers,
            'projects' => Project::ordered()->get(),
            'email' => setting('contact.email', config('portfolio.email')),
            'whatsapp' => preg_replace('/\D+/', '', (string) setting('contact.whatsapp', config('portfolio.whatsapp'))),
            'profiles' => array_filter([
                'LinkedIn' => setting('contact.linkedin', config('portfolio.linkedin')),
                'GitLab' => setting('contact.gitlab', config('portfolio.gitlab')),
                'GitHub' => setting('contact.github', config('portfolio.github')),
                'X (Twitter)' => setting('contact.twitter', config('portfolio.twitter')),
                'Stack Overflow' => setting('contact.stackoverflow', config('portfolio.stackoverflow')),
                'YouTube' => setting('contact.youtube', config('portfolio.youtube')),
            ]),
            'faq' => array_map(
                fn (array $qa): array => ['q' => $qa['q'], 'a' => strtr($qa['a'], $tokens)],
                (array) trans('site.faq.items', [], 'en'),
            ),
            't' => fn (string $key) => trans($key, [], 'en'),
            'tAr' => fn (string $key) => trans($key, [], 'ar'),
        ];
    }

    /**
     * Each entry: ['loc' => url, 'alternates' => [locale => url]].
     *
     * @return array<int, array{loc: string, alternates: array<string, string>, lastmod: ?string}>
     */
    private function urls(): array
    {
        $locales = config('app.supported_locales');
        $urls = [];

        foreach (self::STATIC_ROUTES as $name) {
            array_push($urls, ...$this->entries($name, $locales, [], $name === 'services' ? config('pricing.reviewed') : null));
        }

        // One page per service, each language under its own slug.
        foreach (ServicePages::keys() as $key) {
            array_push($urls, ...$this->entries('services.show', $locales, ServicePages::paramsByLocale($key), config('pricing.reviewed')));
        }

        foreach (Project::ordered()->get() as $project) {
            $params = collect($locales)->mapWithKeys(fn (string $locale): array => [$locale => ['project' => $project->{"slug_{$locale}"}]])->all();
            array_push($urls, ...$this->entries('projects.show', $locales, $params, $project->updated_at?->toDateString()));
        }

        return $urls;
    }

    /**
     * One entry per language for a route, each listing the others as alternates.
     *
     * @param array<int, string> $locales
     * @param array<string, array<string, string>> $paramsByLocale the route's own params in each language (its slug)
     * @return array<int, array{loc: string, alternates: array<string, string>, lastmod: ?string}>
     */
    private function entries(string $name, array $locales, array $paramsByLocale = [], ?string $lastmod = null): array
    {
        $alternates = [];
        foreach ($locales as $locale) {
            $alternates[$locale] = route($name, ['locale' => $locale, ...($paramsByLocale[$locale] ?? [])]);
        }

        return array_map(
            fn (string $locale): array => ['loc' => $alternates[$locale], 'alternates' => $alternates, 'lastmod' => $lastmod],
            $locales,
        );
    }
}
