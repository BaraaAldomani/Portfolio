<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Support\Seo\PageMeta;
use Illuminate\View\View;

final class ProjectsController
{
    public function index(): View
    {
        return view('pages.projects', [
            'meta' => PageMeta::fromLang('projects'),
            'projects' => Project::ordered()->get(),
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
            ),
            'project' => $model,
            'next' => $next,
        ]);
    }
}
