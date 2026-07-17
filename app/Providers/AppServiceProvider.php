<?php

namespace App\Providers;

use App\Models\Capability;
use App\Models\Experience;
use App\Models\FocusItem;
use App\Models\Metric;
use App\Models\ProcessStep;
use App\Models\Project;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Technology;
use App\Observers\ContentObserver;
use App\Support\SiteContent;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Content models whose changes should bust the site content cache.
     */
    private const CONTENT_MODELS = [
        Project::class,
        Service::class,
        Experience::class,
        Metric::class,
        FocusItem::class,
        ProcessStep::class,
        Technology::class,
        Capability::class,
        Setting::class,
    ];

    public function register(): void
    {
        $this->app->singleton(SiteContent::class);
    }

    public function boot(): void
    {
        foreach (self::CONTENT_MODELS as $model) {
            $model::observe(ContentObserver::class);
        }

        // Make the cached content service available to every view as $site.
        View::share('site', $this->app->make(SiteContent::class));
    }
}
