<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Response;

final class SitemapController
{
    /** Static routes that exist in every locale. */
    private const STATIC_ROUTES = ['home', 'services', 'projects.index', 'about', 'contact', 'blog.index'];

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

    public function llms(): Response
    {
        return response()
            ->view('seo.llms')
            ->header('Content-Type', 'text/plain; charset=utf-8');
    }

    /**
     * Each entry: ['loc' => url, 'alternates' => [locale => url]].
     *
     * @return array<int, array{loc: string, alternates: array<string, string>}>
     */
    private function urls(): array
    {
        $locales = config('app.supported_locales');
        $urls = [];

        foreach (self::STATIC_ROUTES as $name) {
            foreach ($locales as $locale) {
                $urls[] = $this->entry($name, ['locale' => $locale], $locales);
            }
        }

        foreach (Project::ordered()->get() as $project) {
            foreach ($locales as $locale) {
                $urls[] = $this->entry('projects.show', ['locale' => $locale, 'project' => $project->{"slug_{$locale}"}], $locales, $project);
            }
        }

        return $urls;
    }

    /**
     * @param array<string, string> $params
     * @param array<int, string> $locales
     * @return array{loc: string, alternates: array<string, string>}
     */
    private function entry(string $name, array $params, array $locales, ?Project $project = null): array
    {
        $alternates = [];
        foreach ($locales as $locale) {
            $alternates[$locale] = $project
                ? route($name, ['locale' => $locale, 'project' => $project->{"slug_{$locale}"}])
                : route($name, ['locale' => $locale]);
        }

        return ['loc' => route($name, $params), 'alternates' => $alternates];
    }
}
