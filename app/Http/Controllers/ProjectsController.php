<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Support\Seo\JsonLd;
use App\Support\Seo\PageMeta;
use Illuminate\Http\RedirectResponse;
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

    public function show(string $locale, string $project): View|RedirectResponse
    {
        $model = Project::where('slug_' . app()->getLocale(), $project)->first();

        if ($model === null) {
            // The other language's slug (a link whose language was switched by
            // hand) still lands on the right page, as it does for services.
            foreach (config('app.supported_locales') as $other) {
                $match = $other !== $locale ? Project::where('slug_' . $other, $project)->first() : null;
                if ($match !== null) {
                    return redirect()->route('projects.show', ['locale' => $locale, 'project' => $match->slug()], 301);
                }
            }

            abort(404);
        }

        // "Next project" wraps around so the reader can keep going.
        $next = Project::ordered()->where('sort_order', '>', $model->sort_order)->first()
            ?? Project::ordered()->whereKeyNot($model->getKey())->first();

        // The title a results page shows: the project, then whose work it is,
        // when both fit in the 60 characters a result has room for.
        $title = $model->localized('title');
        $signed = $title . ' | ' . __('common.brand');

        return view('pages.project-show', [
            'meta' => PageMeta::forContent(
                mb_strlen($signed) <= 60 ? $signed : $title,
                $model->localized('summary'),
                $model->slugsByLocale(),
                [JsonLd::project($model, route('projects.show', ['locale' => app()->getLocale(), 'project' => $model->slug()]))],
                $model->hasCover() ? $model->coverImage() : null,
                $title,
            ),
            'project' => $model,
            'next' => $next,
        ]);
    }
}
