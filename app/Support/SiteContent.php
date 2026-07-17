<?php

namespace App\Support;

use App\Models\Capability;
use App\Models\Experience;
use App\Models\FocusItem;
use App\Models\Metric;
use App\Models\ProcessStep;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Technology;
use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Throwable;

/**
 * Single read layer for all dashboard-managed content. Blade and controllers
 * read structural content through this service instead of the old lang-file
 * arrays.
 *
 * The settings map (a plain array) is cached persistently. The content
 * collections are memoised per request only — they are small, indexed tables,
 * and Eloquent collections do not round-trip cleanly through the database
 * cache store. flush() clears the settings cache; the observer that calls it
 * runs on save, so the next public request re-reads fresh data.
 */
class SiteContent
{
    /** @var array<string, Collection<int, \Illuminate\Database\Eloquent\Model>> */
    private array $memo = [];

    /**
     * Whole settings map keyed as "group.key" => value.
     *
     * @return array<string, mixed>
     */
    public function settings(): array
    {
        return Cache::rememberForever('site:settings', function (): array {
            try {
                return Setting::all()
                    ->mapWithKeys(fn (Setting $s): array => ["{$s->group}.{$s->key}" => $s->value])
                    ->all();
            } catch (Throwable) {
                // Table may not exist yet (e.g. during initial migrate).
                return [];
            }
        });
    }

    public function setting(string $key, mixed $default = null): mixed
    {
        return $this->settings()[$key] ?? $default;
    }

    /** @return Collection<int, Service> */
    public function services(): Collection
    {
        return $this->remember('services', fn () => Service::ordered()->get());
    }

    /** @return Collection<int, Experience> */
    public function experiences(): Collection
    {
        return $this->remember('experiences', fn () => Experience::ordered()->get());
    }

    /** @return Collection<int, Metric> */
    public function metrics(string $context = 'home'): Collection
    {
        return $this->remember("metrics.{$context}", fn () => Metric::context($context)->ordered()->get());
    }

    /** @return Collection<int, FocusItem> */
    public function focusItems(): Collection
    {
        return $this->remember('focus', fn () => FocusItem::ordered()->get());
    }

    /** @return Collection<int, ProcessStep> */
    public function processSteps(): Collection
    {
        return $this->remember('process', fn () => ProcessStep::ordered()->get());
    }

    /** @return Collection<int, Technology> */
    public function technologies(): Collection
    {
        return $this->remember('technologies', fn () => Technology::ordered()->get());
    }

    /** @return Collection<int, Capability> */
    public function capabilities(): Collection
    {
        return $this->remember('capabilities', fn () => Capability::ordered()->get());
    }

    public function flush(): void
    {
        Cache::forget('site:settings');
        $this->memo = [];
    }

    /**
     * Per-request memoisation for a content collection.
     *
     * @param  Closure(): Collection<int, \Illuminate\Database\Eloquent\Model>  $query
     * @return Collection<int, \Illuminate\Database\Eloquent\Model>
     */
    private function remember(string $key, Closure $query): Collection
    {
        return $this->memo[$key] ??= $query();
    }
}
