<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Support\Seo\JsonLd;
use App\Support\Seo\PageMeta;
use Illuminate\View\View;

final class ProjectsController
{
    public function index(): View
    {
        $projects = Project::ordered()->get();

        return view('pages.projects', [
            'meta' => PageMeta::fromLang('projects', [JsonLd::projectList($projects)]),
            'projects' => $projects,
        ]);
    }

    public function show(string $locale, string $project): View
    {
        $model = Project::where('slug_' . app()->getLocale(), $project)->firstOrFail();

        // "Next project" wraps around so the reader can keep going.
        $next = Project::ordered()->where('sort_order', '>', $model->sort_order)->first()
            ?? Project::ordered()->whereKeyNot($model->getKey())->first();

        return view('pages.project-show', [
            'meta' => PageMeta::forContent(
                $model->localized('title'),
                $model->localized('summary'),
                $model->slugsByLocale(),
                [JsonLd::project($model, route('projects.show', ['locale' => app()->getLocale(), 'project' => $model->slug()]))],
                $model->hasCover() ? $model->coverImage() : null,
            ),
            'project' => $model,
            'next' => $next,
        ]);
    }
}
