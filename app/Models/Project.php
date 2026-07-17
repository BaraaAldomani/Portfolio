<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedContent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

final class Project extends Model
{
    use HasLocalizedContent;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'highlights_ar' => 'array',
            'highlights_en' => 'array',
            'featured' => 'boolean',
        ];
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('featured', true)->orderBy('sort_order');
    }

    /**
     * @return array<int, string>
     */
    public function highlights(): array
    {
        return $this->localizedArray('highlights');
    }

    public function slug(): string
    {
        return $this->{'slug_' . app()->getLocale()};
    }

    /**
     * Public URL of the cover image. Uses the DB `image_path` when set (so it
     * can be swapped per project later), otherwise falls back to the slug-based
     * placeholder under config('portfolio.images.projects_dir').
     */
    public function coverImage(): string
    {
        $path = $this->image_path ?: config('portfolio.images.projects_dir') . '/' . $this->slug_en . '.svg';

        return image_url($path);
    }

    /**
     * Route params per locale, for hreflang alternates of the show page.
     *
     * @return array<string, array<string, string>>
     */
    public function slugsByLocale(): array
    {
        return collect(config('app.supported_locales'))
            ->mapWithKeys(fn (string $locale) => [$locale => ['project' => $this->{"slug_{$locale}"}]])
            ->all();
    }
}
